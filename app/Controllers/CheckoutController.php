<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Auth;
use App\Core\Database;
use App\Models\Cart;
use App\Models\Checkout;
final class CheckoutController {
  public static function slots(): array {
    $wins = ['10:00–12:00', '12:00–14:00', '14:00–16:00', '16:00–18:00'];
    $out = [];
    for ($d = 0; $d < 3; $d++) {
      $t = strtotime("+$d days");
      foreach ($wins as $w) $out[] = date('D, j M', $t) . ' · ' . $w;
    }
    return $out;
  }
  public static function show(): void {
    Auth::requireLogin();
    $t = Cart::totals($_SESSION['coupon'] ?? null);
    if (!$t['count']) { redirect('/cart'); }
    $pdo = Database::pdo();
    $st = $pdo->prepare('SELECT * FROM addresses WHERE user_id=? ORDER BY is_default DESC');
    $st->execute([$_SESSION['uid']]); $addrs = $st->fetchAll();
    $cfg = require dirname(__DIR__, 2) . '/config/app.php';
    $qrFile = dirname(__DIR__, 2) . '/public/assets/images/payment-qr.jpg';
    view('checkout', ['title' => 'Checkout'] + $t + ['addrs' => $addrs, 'fees' => $cfg['delivery_fees'], 'free_over' => $cfg['free_shipping_over'], 'slots' => self::slots(), 'qr' => is_file($qrFile) ? '/assets/images/payment-qr.jpg' : null]);
  }
  public static function place(): void {
    Auth::requireLogin(); require_post();
    $_SESSION['_old'] = $_POST;
    try {
      foreach (['name','phone','line1','city','postcode'] as $k) {
        if (trim($_POST[$k] ?? '') === '') throw new \RuntimeException('Please complete delivery details');
      }
      $slot = trim($_POST['slot'] ?? '');
      if ($slot !== '' && !in_array($slot, self::slots(), true)) throw new \RuntimeException('Invalid delivery slot');
      $r = Checkout::place((int)$_SESSION['uid'], [
        'name' => trim($_POST['name']), 'phone' => trim($_POST['phone']),
        'line1' => trim($_POST['line1']), 'city' => trim($_POST['city']),
        'postcode' => trim($_POST['postcode']), 'delivery' => $_POST['delivery'] ?? 'standard',
        'payment' => $_POST['payment'] ?? 'cod', 'coupon' => $_SESSION['coupon'] ?? '',
        'slot' => $slot, 'cash' => $_POST['cash'] ?? null, 'change' => $_POST['change'] ?? null,
      ]);
      unset($_SESSION['coupon'], $_SESSION['_old']);
      redirect('/orders/' . $r['order_id'] . '?placed=1');
    } catch (\Throwable $e) {
      flash('error', $e->getMessage()); redirect('/checkout');
    }
  }
}
