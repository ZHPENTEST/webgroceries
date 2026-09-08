<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Database;
final class Subscription {
  public static function boxes(): array {
    return Database::pdo()->query("SELECT p.*, c.name cat_name FROM products p JOIN categories c ON c.id=p.category_id WHERE c.slug='langganan' AND p.status='active' ORDER BY p.id")->fetchAll();
  }
  public static function mine(int $uid): array {
    $st = Database::pdo()->prepare('SELECT s.*, p.name pname, p.image pimage FROM subscriptions s JOIN products p ON p.id=s.product_id WHERE s.user_id=? ORDER BY s.id DESC');
    $st->execute([$uid]); return $st->fetchAll();
  }
  public static function nextDate(int $dow): string {
    $t = strtotime('next ' . ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][$dow % 7]);
    return date('Y-m-d', $t);
  }
  public static function generate(int $subId): array {
    $pdo = Database::pdo();
    $pdo->beginTransaction();
    try {
      $st = $pdo->prepare('SELECT s.*, p.price, p.discount_price, p.stock_quantity, p.name FROM subscriptions s JOIN products p ON p.id=s.product_id WHERE s.id=? AND s.status="active" FOR UPDATE');
      $st->execute([$subId]); $s = $st->fetch();
      if (!$s) throw new \RuntimeException('Subscription not found/active');
      if ($s['next_run'] > date('Y-m-d')) throw new \RuntimeException('Not due yet');
      $qty = max(1, min(4, (int)$s['qty']));
      if ($qty > (int)$s['stock_quantity']) throw new \RuntimeException('Box out of stock');
      $unit = ($s['discount_price'] !== null && (float)$s['discount_price'] < (float)$s['price']) ? (float)$s['discount_price'] : (float)$s['price'];
      $sub = $unit * $qty;
      $digits = preg_replace('/\D+/', '', $s['postcode']);
      $z = $pdo->prepare('SELECT fee FROM delivery_zones WHERE status="active" AND ? LIKE CONCAT(prefix,"%") ORDER BY LENGTH(prefix) DESC LIMIT 1');
      $z->execute([$digits]); $zr = $z->fetch();
      $fee = $zr ? (float)$zr['fee'] : 4.90;
      if ($sub >= 80) $fee = 0;
      $total = $sub + $fee;
      $earn = (int)floor($total);
      $num = 'WG-SUB-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
      $pdo->prepare('INSERT INTO orders (order_number,user_id,recipient,phone,address_line,city,postcode,delivery_method,delivery_fee,payment_method,payment_status,subtotal,discount,total,points_used,points_earned,status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')
        ->execute([$num, $s['user_id'], $s['recipient'], $s['phone'], $s['line1'], $s['city'], $s['postcode'], 'standard', $fee, $s['payment_method'], 'unpaid', $sub, 0, $total, 0, $earn, 'pending']);
      $oid = (int)$pdo->lastInsertId();
      $pdo->prepare('INSERT INTO order_items (order_id,product_id,product_name,unit_price,quantity,line_total) VALUES (?,?,?,?,?,?)')
        ->execute([$oid, $s['product_id'], $s['name'] . ' (langganan)', $unit, $qty, $sub]);
      $pdo->prepare('UPDATE products SET stock_quantity = stock_quantity - ? WHERE id=?')->execute([$qty, $s['product_id']]);
      $pdo->prepare('INSERT INTO payments (order_id,provider,reference,amount,status) VALUES (?,?,?,?,?)')->execute([$oid, $s['payment_method'], null, $total, 'pending']);
      $pdo->prepare('UPDATE users SET points = points + ? WHERE id=?')->execute([$earn, $s['user_id']]);
      $pdo->prepare('UPDATE subscriptions SET next_run = DATE_ADD(next_run, INTERVAL 7 DAY) WHERE id=?')->execute([$subId]);
      $pdo->commit();
      return ['order_id' => $oid, 'order_number' => $num];
    } catch (\Throwable $e) { $pdo->rollBack(); throw $e; }
  }
}
