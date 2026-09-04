<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Database;
use App\Models\Product;
final class Cart {
  private static function owner(): array {
    if (isset($_SESSION['uid'])) return ['col' => 'user_id', 'val' => (int)$_SESSION['uid']];
    if (empty($_SESSION['guest_cart'])) $_SESSION['guest_cart'] = bin2hex(random_bytes(16));
    return ['col' => 'session_id', 'val' => $_SESSION['guest_cart']];
  }
  public static function items(): array {
    $o = self::owner(); $pdo = Database::pdo();
    $st = $pdo->prepare("SELECT ci.*, p.name, p.slug, p.image, p.price, p.discount_price, p.stock_quantity, p.unit FROM cart_items ci JOIN products p ON p.id=ci.product_id WHERE ci.{$o['col']}=?");
    $st->execute([$o['val']]); return $st->fetchAll();
  }
  public static function add(int $pid, int $qty): void {
    $p = Product::byId($pid);
    if (!$p || $p['status'] !== 'active') throw new \RuntimeException('Product unavailable');
    $qty = max(1, min(99, $qty));
    if ($qty > (int)$p['stock_quantity']) throw new \RuntimeException('Only ' . $p['stock_quantity'] . ' in stock');
    $o = self::owner(); $pdo = Database::pdo();
    $st = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE {$o['col']}=? AND product_id=? LIMIT 1");
    $st->execute([$o['val'], $pid]); $row = $st->fetch();
    if ($row) {
      $new = min(99, $row['quantity'] + $qty);
      if ($new > (int)$p['stock_quantity']) throw new \RuntimeException('Exceeds available stock');
      $pdo->prepare('UPDATE cart_items SET quantity=? WHERE id=?')->execute([$new, $row['id']]);
    } else {
      $pdo->prepare("INSERT INTO cart_items ({$o['col']},product_id,quantity) VALUES (?,?,?)")->execute([$o['val'], $pid, $qty]);
    }
  }
  public static function setQty(int $pid, int $qty): void {
    $o = self::owner(); $pdo = Database::pdo();
    if ($qty <= 0) { $pdo->prepare("DELETE FROM cart_items WHERE {$o['col']}=? AND product_id=?")->execute([$o['val'], $pid]); return; }
    $p = Product::byId($pid);
    if ($qty > (int)$p['stock_quantity']) throw new \RuntimeException('Exceeds stock');
    $pdo->prepare("UPDATE cart_items SET quantity=? WHERE {$o['col']}=? AND product_id=?")->execute([min(99, $qty), $o['val'], $pid]);
  }
  public static function remove(int $pid): void {
    $o = self::owner();
    Database::pdo()->prepare("DELETE FROM cart_items WHERE {$o['col']}=? AND product_id=?")->execute([$o['val'], $pid]);
  }
  public static function clear(): void {
    $o = self::owner();
    Database::pdo()->prepare("DELETE FROM cart_items WHERE {$o['col']}=?")->execute([$o['val']]);
  }
  public static function totals(?string $coupon = null): array {
    $items = self::items(); $sub = 0;
    foreach ($items as $i) $sub += Product::effectivePrice($i) * (int)$i['quantity'];
    $disc = 0;
    if ($coupon) {
      $st = Database::pdo()->prepare('SELECT * FROM coupons WHERE code=? AND status="active" AND (expires_at IS NULL OR expires_at >= CURDATE()) LIMIT 1');
      $st->execute([$coupon]); $c = $st->fetch();
      if ($c && $sub >= (float)$c['min_subtotal']) {
        $disc = $c['type'] === 'percent' ? $sub * ((float)$c['value'] / 100) : min((float)$c['value'], $sub);
      }
    }
    return ['items' => $items, 'count' => array_sum(array_column($items, 'quantity')), 'subtotal' => $sub, 'discount' => $disc];
  }
  public static function mergeOnLogin(int $uid): void {
    if (empty($_SESSION['guest_cart'])) return;
    $pdo = Database::pdo();
    $pdo->prepare('UPDATE cart_items SET user_id=?, session_id=NULL WHERE session_id=?')->execute([$uid, $_SESSION['guest_cart']]);
    unset($_SESSION['guest_cart']);
  }
}
