<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Auth;
use App\Core\Database;
final class OrderController {
  public static function index(): void {
    Auth::requireLogin();
    $st = Database::pdo()->prepare('SELECT * FROM orders WHERE user_id=? ORDER BY id DESC');
    $st->execute([$_SESSION['uid']]);
    view('orders', ['title' => 'My orders', 'orders' => $st->fetchAll()]);
  }
  public static function cancel(int $id): void {
    Auth::requireLogin(); require_post();
    $pdo = Database::pdo();
    $pdo->beginTransaction();
    try {
      $st = $pdo->prepare('SELECT * FROM orders WHERE id=? AND user_id=? LIMIT 1 FOR UPDATE');
      $st->execute([$id, $_SESSION['uid']]); $o = $st->fetch();
      if (!$o) throw new \RuntimeException('Order not found');
      if ($o['status'] !== 'pending') throw new \RuntimeException('Only pending orders can be cancelled');
      $pdo->prepare('UPDATE orders SET status="cancelled" WHERE id=?')->execute([$id]);
      $it = $pdo->prepare('SELECT product_id, quantity FROM order_items WHERE order_id=?'); $it->execute([$id]);
      $rs = $pdo->prepare('UPDATE products SET stock_quantity = stock_quantity + ? WHERE id=?');
      foreach ($it->fetchAll() as $r) $rs->execute([$r['quantity'], $r['product_id']]);
      $pdo->commit();
      flash('ok', 'Order cancelled, stock restored'); redirect('/orders/' . $id);
    } catch (\Throwable $e) { $pdo->rollBack(); flash('error', $e->getMessage()); redirect('/orders/' . $id); }
  }
  public static function reorder(int $id): void {
    Auth::requireLogin(); require_post();
    $pdo = Database::pdo();
    $st = $pdo->prepare('SELECT id FROM orders WHERE id=? AND user_id=? LIMIT 1');
    $st->execute([$id, $_SESSION['uid']]);
    if (!$st->fetch()) { http_response_code(404); exit('Not found'); }
    $it = $pdo->prepare('SELECT product_id, quantity FROM order_items WHERE order_id=?'); $it->execute([$id]);
    $n = 0;
    foreach ($it->fetchAll() as $r) {
      try { \App\Models\Cart::add((int)$r['product_id'], (int)$r['quantity']); $n++; }
      catch (\Throwable $e) { /* out of stock — skip */ }
    }
    flash($n ? 'ok' : 'error', $n ? "$n items added back to cart" : 'Nothing available to reorder');
    redirect('/cart');
  }
  public static function show(int $id): void {
    Auth::requireLogin();
    $pdo = Database::pdo();
    $st = $pdo->prepare('SELECT * FROM orders WHERE id=? AND user_id=? LIMIT 1');
    $st->execute([$id, $_SESSION['uid']]); $o = $st->fetch();
    if (!$o) { http_response_code(404); view('errors/404', ['title' => 'Not found']); return; }
    $it = $pdo->prepare('SELECT * FROM order_items WHERE order_id=?'); $it->execute([$id]);
    $py = $pdo->prepare('SELECT * FROM payments WHERE order_id=? LIMIT 1'); $py->execute([$id]);
    $qrFile = dirname(__DIR__, 2) . '/public/assets/images/payment-qr.jpg';
    $items = $it->fetchAll();
    // WhatsApp order link (free wa.me, no API)
    $waLink = null;
    if ($mw = wa_number(\App\Models\Settings::get('merchant_whatsapp') ?? '')) {
      $lines = ['*ORDER ' . $o['order_number'] . ' - WebGroceries*', '--------------------------'];
      foreach ($items as $i) $lines[] = $i['quantity'] . 'x ' . $i['product_name'] . ' - ' . money((float)$i['line_total']);
      $lines[] = '--------------------------';
      $lines[] = 'Subtotal: ' . money((float)$o['subtotal']);
      $lines[] = 'Diskaun: -' . money((float)$o['discount']);
      $lines[] = 'Penghantaran (' . $o['delivery_method'] . '): ' . money((float)$o['delivery_fee']);
      $lines[] = '*Jumlah: ' . money((float)$o['total']) . '*';
      $lines[] = 'Bayaran: ' . $o['payment_method'];
      $lines[] = 'Nama: ' . $o['recipient'] . ' (' . $o['phone'] . ')';
      $lines[] = 'Alamat: ' . $o['address_line'] . ', ' . $o['city'] . ' ' . $o['postcode'];
      if (!empty($o['latitude'])) $lines[] = 'Pin: https://www.google.com/maps?q=' . $o['latitude'] . ',' . $o['longitude'];
      $waLink = 'https://wa.me/' . $mw . '?text=' . rawurlencode(implode("\n", $lines));
    }
    view('order_detail', ['title' => 'Order ' . $o['order_number'], 'o' => $o, 'items' => $items, 'placed' => isset($_GET['placed']), 'pay' => $py->fetch() ?: null, 'qr' => is_file($qrFile) ? '/assets/images/payment-qr.jpg' : null, 'waLink' => $waLink]);
  }
  public static function claimPaid(int $id): void {
    Auth::requireLogin(); require_post();
    $pdo = Database::pdo();
    $st = $pdo->prepare('SELECT * FROM orders WHERE id=? AND user_id=? AND payment_method="transfer" LIMIT 1');
    $st->execute([$id, $_SESSION['uid']]); $o = $st->fetch();
    if (!$o) { http_response_code(404); exit('Not found'); }
    $py = $pdo->prepare('SELECT * FROM payments WHERE order_id=? LIMIT 1'); $py->execute([$id]);
    if (($py->fetch()['status'] ?? '') !== 'pending') { flash('error', 'Nothing to confirm'); redirect('/orders/' . $id); }
    $pdo->prepare('UPDATE payments SET status="claimed" WHERE order_id=?')->execute([$id]);
    flash('ok', 'Terima kasih! Admin akan sahkan bayaran anda'); redirect('/orders/' . $id);
  }
}
