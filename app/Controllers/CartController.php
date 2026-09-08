<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Auth;
use App\Core\Database;
use App\Models\Cart;
final class CartController {
  public static function page(): void { view('cart', ['title' => 'Your cart'] + Cart::totals($_SESSION['coupon'] ?? null)); }
  public static function api(string $action): void {
    header('Content-Type: application/json');
    if ($action === 'zone') {
      $digits = preg_replace('/\D+/', '', $_GET['postcode'] ?? '');
      $st = \App\Core\Database::pdo()->prepare('SELECT name, fee FROM delivery_zones WHERE status="active" AND ? LIKE CONCAT(prefix,"%") ORDER BY LENGTH(prefix) DESC LIMIT 1');
      $st->execute([$digits]); $z = $st->fetch();
      $std = $z ? (float)$z['fee'] : 4.90;
      json_out(['ok' => true, 'zone' => $z['name'] ?? 'Standard', 'standard' => $std, 'express' => $std + 5, 'scheduled' => $std + 2]);
    }
    try {
      if ($action === 'add') {
        if (!\App\Core\Csrf::verify($_POST['csrf'] ?? $_SERVER['HTTP_X_CSRF'] ?? null)) json_out(['ok' => false, 'error' => 'CSRF mismatch'], 419);
        Cart::add((int)($_POST['id'] ?? 0), (int)($_POST['qty'] ?? 1));
      } elseif ($action === 'qty') {
        Cart::setQty((int)($_POST['id'] ?? 0), (int)($_POST['qty'] ?? 1));
      } elseif ($action === 'remove') {
        Cart::remove((int)($_POST['id'] ?? 0));
      } elseif ($action === 'coupon') {
        $_SESSION['coupon'] = trim($_POST['code'] ?? '');
      }
      $t = Cart::totals($_SESSION['coupon'] ?? null);
      json_out(['ok' => true, 'count' => $t['count'], 'subtotal' => $t['subtotal'], 'discount' => $t['discount']]);
    } catch (\Throwable $e) { json_out(['ok' => false, 'error' => $e->getMessage()], 400); }
  }
}
