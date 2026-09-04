<?php
declare(strict_types=1);
namespace App\Core;
final class Auth {
  public static function user(): ?array {
    if (!isset($_SESSION['uid'])) return null;
    $st = Database::pdo()->prepare('SELECT id,name,email,phone,avatar,role,status FROM users WHERE id=? LIMIT 1');
    $st->execute([(int)$_SESSION['uid']]);
    $u = $st->fetch();
    return $u ?: null;
  }
  public static function check(): bool { return self::user() !== null; }
  public static function isAdmin(): bool { $u = self::user(); return $u && $u['role'] === 'admin'; }
  public static function requireLogin(): void {
    if (!self::check()) { redirect('/login'); }
  }
  public static function requireAdmin(): void {
    if (!self::isAdmin()) { http_response_code(403); view('errors/403', ['title' => 'Forbidden']); exit; }
  }
  public static function login(int $uid): void {
    session_regenerate_id(true);
    $_SESSION['uid'] = $uid;
  }
  public static function logout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
      $p = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
  }
}
