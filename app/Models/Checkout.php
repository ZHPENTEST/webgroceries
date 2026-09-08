<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Database;
use App\Models\Product;
final class Checkout {
  public static function place(int $uid, array $input): array {
    $pdo = Database::pdo();
    $cfg = require dirname(__DIR__, 2) . '/config/app.php';
    $fees = $cfg['delivery_fees'];
    $pdo->beginTransaction();
    try {
      // Lock cart products
      $st = $pdo->prepare('SELECT ci.*, p.price, p.discount_price, p.stock_quantity, p.name FROM cart_items ci JOIN products p ON p.id=ci.product_id WHERE ci.user_id=? FOR UPDATE');
      $st->execute([$uid]); $items = $st->fetchAll();
      if (!$items) throw new \RuntimeException('Your cart is empty');
      $sub = 0;
      foreach ($items as $i) {
        if ($i['quantity'] > (int)$i['stock_quantity']) throw new \RuntimeException('Insufficient stock for ' . $i['name']);
        $sub += Product::effectivePrice($i) * (int)$i['quantity'];
      }
      // Coupon (recalculate server-side)
      $disc = 0; $code = trim($input['coupon'] ?? '');
      if ($code !== '') {
        $cs = $pdo->prepare('SELECT * FROM coupons WHERE code=? AND status="active" AND (expires_at IS NULL OR expires_at>=CURDATE()) LIMIT 1 FOR UPDATE');
        $cs->execute([$code]); $c = $cs->fetch();
        if (!$c || $sub < (float)$c['min_subtotal']) throw new \RuntimeException('Invalid coupon');
        if ($c['usage_limit'] !== null && (int)$c['used_count'] >= (int)$c['usage_limit']) throw new \RuntimeException('Coupon exhausted');
        $disc = $c['type'] === 'percent' ? $sub * ((float)$c['value'] / 100) : min((float)$c['value'], $sub);
        $pdo->prepare('UPDATE coupons SET used_count=used_count+1 WHERE id=?')->execute([$c['id']]);
      }
      $method = in_array($input['delivery'] ?? '', ['standard','express','scheduled']) ? $input['delivery'] : 'standard';
      // Delivery zone by postcode (longest prefix wins; '' prefix = fallback)
      $digits = preg_replace('/\D+/', '', $input['postcode'] ?? '');
      $z = $pdo->prepare('SELECT name, fee FROM delivery_zones WHERE status="active" AND ? LIKE CONCAT(prefix,"%") ORDER BY LENGTH(prefix) DESC LIMIT 1');
      $z->execute([$digits]); $zone = $z->fetch();
      $zstd = $zone ? (float)$zone['fee'] : (float)$fees['standard'];
      $zfee = ['standard' => $zstd, 'express' => $zstd + 5, 'scheduled' => $zstd + 2];
      // Loyalty: 100 pts = RM1, capped by payable subtotal
      $ub = $pdo->prepare('SELECT points FROM users WHERE id=? FOR UPDATE'); $ub->execute([$uid]);
      $bal = (int)($ub->fetch()['points'] ?? 0);
      $used = 0; $ptsVal = 0;
      if (!empty($input['use_points']) && $bal >= 100) {
        $ptsVal = (float)min((int)floor($bal / 100), max(0, $sub - $disc));
        $used = (int)($ptsVal * 100);
      }
      $fee = ($sub - $disc - $ptsVal) >= $cfg['free_shipping_over'] && $method === 'standard' ? 0 : $zfee[$method];
      $total = max(0, $sub - $disc - $ptsVal + $fee);
      $earn = (int)floor($total);
      $pay = in_array($input['payment'] ?? '', ['cod','mock_online','transfer']) ? $input['payment'] : 'cod';
      $cash = $pay === 'cod' ? max(0, (float)($input['cash'] ?? 0)) : null;
      $change = ($pay === 'cod' && !empty($input['change'])) ? 1 : 0;
      if ($change && $cash <= 0) throw new \RuntimeException('Isi jumlah duit untuk sediakan baki');
      $num = 'WG-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
      $pdo->prepare('INSERT INTO orders (order_number,user_id,recipient,phone,address_line,city,postcode,delivery_method,delivery_fee,payment_method,payment_status,subtotal,discount,total,coupon_code,cash_tendered,needs_change,latitude,longitude,points_used,points_earned,status,scheduled_slot) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')
        ->execute([$num, $uid, $input['name'], $input['phone'], $input['line1'], $input['city'], $input['postcode'], $method, $fee, $pay, $pay === 'mock_online' ? 'paid' : 'unpaid', $sub, $disc, $total, $code ?: null, $cash ?: null, $change, $input['lat'] ?? null, $input['lng'] ?? null, $used, $earn, 'pending', $input['slot'] ?? null]);
      $oid = (int)$pdo->lastInsertId();
      foreach ($items as $i) {
        $unit = Product::effectivePrice($i);
        $pdo->prepare('INSERT INTO order_items (order_id,product_id,product_name,unit_price,quantity,line_total) VALUES (?,?,?,?,?,?)')
          ->execute([$oid, $i['product_id'], $i['name'], $unit, $i['quantity'], $unit * $i['quantity']]);
        $aff = $pdo->prepare('UPDATE products SET stock_quantity = stock_quantity - ? WHERE id=? AND stock_quantity >= ?')->execute([$i['quantity'], $i['product_id'], $i['quantity']]);
        if (!$aff) throw new \RuntimeException('Stock changed during checkout');
      }
      $pdo->prepare('INSERT INTO payments (order_id,provider,reference,amount,status) VALUES (?,?,?,?,?)')
        ->execute([$oid, $pay, $pay === 'mock_online' ? 'MOCK-' . bin2hex(random_bytes(6)) : null, $total, $pay === 'mock_online' ? 'paid' : 'pending']);
      $pdo->prepare('DELETE FROM cart_items WHERE user_id=?')->execute([$uid]);
      $pdo->prepare('UPDATE users SET points = points - ? + ? WHERE id=?')->execute([$used, $earn, $uid]);
      $pdo->commit();
      return ['order_id' => $oid, 'order_number' => $num, 'total' => $total];
    } catch (\Throwable $e) { $pdo->rollBack(); throw $e; }
  }
}
