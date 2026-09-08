<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Auth;
use App\Core\Database;
use App\Models\Subscription;
final class SubscriptionController {
  public static function index(): void {
    $mine = Auth::check() ? Subscription::mine((int)$_SESSION['uid']) : [];
    view('subscriptions', ['title' => 'Kotak langganan mingguan', 'boxes' => Subscription::boxes(), 'mine' => $mine]);
  }
  public static function subscribe(): void {
    Auth::requireLogin(); require_post();
    $pdo = Database::pdo();
    $pid = (int)($_POST['box_id'] ?? 0);
    $st = $pdo->prepare("SELECT p.id FROM products p JOIN categories c ON c.id=p.category_id WHERE p.id=? AND c.slug='langganan' AND p.status='active' LIMIT 1");
    $st->execute([$pid]);
    if (!$st->fetch()) { flash('error', 'Box tidak sah'); redirect('/langganan'); }
    foreach (['recipient','phone','line1','city','postcode'] as $k) {
      if (trim($_POST[$k] ?? '') === '') { flash('error', 'Lengkapkan alamat'); redirect('/langganan'); }
    }
    $dow = max(0, min(6, (int)($_POST['day'] ?? 1)));
    $pay = ($_POST['payment'] ?? 'cod') === 'transfer' ? 'transfer' : 'cod';
    $pdo->prepare('INSERT INTO subscriptions (user_id,product_id,qty,recipient,phone,line1,city,postcode,day_of_week,payment_method,next_run) VALUES (?,?,?,?,?,?,?,?,?,?,?)')
      ->execute([$_SESSION['uid'], $pid, max(1, min(4, (int)($_POST['qty'] ?? 1))), trim($_POST['recipient']), trim($_POST['phone']), trim($_POST['line1']), trim($_POST['city']), trim($_POST['postcode']), $dow, $pay, Subscription::nextDate($dow)]);
    flash('ok', 'Langganan aktif — kotak pertama tiba minggu ini'); redirect('/langganan');
  }
  public static function setStatus(int $id, string $to): void {
    Auth::requireLogin(); require_post();
    if (!in_array($to, ['paused','cancelled','active'], true)) { http_response_code(400); exit('Bad'); }
    $pdo = Database::pdo();
    if ($to === 'active') {
      $pdo->prepare('UPDATE subscriptions SET status="active", next_run=GREATEST(next_run, CURDATE()) WHERE id=? AND user_id=? AND status="paused"')->execute([$id, $_SESSION['uid']]);
    } else {
      $pdo->prepare('UPDATE subscriptions SET status=? WHERE id=? AND user_id=? AND status!="cancelled"')->execute([$to, $id, $_SESSION['uid']]);
    }
    redirect('/langganan');
  }
}
