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
    view('order_detail', ['title' => 'Order ' . $o['order_number'], 'o' => $o, 'items' => $it->fetchAll(), 'placed' => isset($_GET['placed'])]);
  }
}
