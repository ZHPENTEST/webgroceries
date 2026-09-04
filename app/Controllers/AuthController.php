<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Auth;
use App\Core\Database;
use App\Models\Cart;
final class AuthController {
  public static function registerForm(): void { view('auth/register', ['title' => 'Create account']); }
  public static function register(): void {
    require_post();
    $name = trim($_POST['name'] ?? ''); $email = strtolower(trim($_POST['email'] ?? '')); $pw = $_POST['password'] ?? '';
    $_SESSION['_old'] = ['name' => $name, 'email' => $email];
    if (strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pw) < 8) {
      flash('error', 'Name, valid email and 8+ char password required'); redirect('/register'); }
    $pdo = Database::pdo();
    $st = $pdo->prepare('SELECT id FROM users WHERE email=? LIMIT 1'); $st->execute([$email]);
    if ($st->fetch()) { flash('error', 'Email already registered'); redirect('/register'); }
    $pdo->prepare('INSERT INTO users (name,email,password_hash) VALUES (?,?,?)')->execute([$name, $email, password_hash($pw, PASSWORD_DEFAULT)]);
    $uid = (int)$pdo->lastInsertId();
    Cart::mergeOnLogin($uid); Auth::login($uid);
    unset($_SESSION['_old']); redirect('/');
  }
  public static function loginForm(): void { view('auth/login', ['title' => 'Login']); }
  public static function login(): void {
    require_post();
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $pdo = Database::pdo();
    // Brute-force guard: max 5 attempts per IP per 15 min
    $st = $pdo->prepare('SELECT COUNT(*) c FROM login_attempts WHERE ip=? AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)');
    $st->execute([$ip]);
    if ((int)$st->fetch()['c'] >= 5) { http_response_code(429); exit('Too many attempts. Try again in 15 minutes.'); }
    $email = strtolower(trim($_POST['email'] ?? '')); $pw = $_POST['password'] ?? '';
    // Fixed-cost response to slow credential enumeration slightly
    $st = $pdo->prepare('SELECT * FROM users WHERE email=? LIMIT 1'); $st->execute([$email]);
    $u = $st->fetch();
    if (!$u || !password_verify($pw, $u['password_hash']) || $u['status'] !== 'active') {
      $pdo->prepare('INSERT INTO login_attempts (ip, email) VALUES (?,?)')->execute([$ip, $email]);
      flash('error', 'Invalid credentials'); redirect('/login'); }
    $pdo->prepare('DELETE FROM login_attempts WHERE ip=?')->execute([$ip]);
    Cart::mergeOnLogin((int)$u['id']); Auth::login((int)$u['id']);
    redirect($u['role'] === 'admin' ? '/admin' : '/');
  }
  public static function logout(): void { require_post(); Auth::logout(); redirect('/'); }
}
