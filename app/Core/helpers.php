<?php
declare(strict_types=1);
use App\Core\Csrf;
function e(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function redirect(string $path): void { header('Location: ' . $path); exit; }
function redirect_back(string $fallback = '/'): void {
  // Open-redirect guard: only allow local absolute paths
  $ref = $_SERVER['HTTP_REFERER'] ?? $fallback;
  $p = parse_url($ref, PHP_URL_PATH) ?: $fallback;
  if (!str_starts_with($p, '/') || str_starts_with($p, '//')) $p = $fallback;
  redirect($p);
}
function old(string $k, string $d = ''): string { return e($_SESSION['_old'][$k] ?? $d); }
function flash(string $k, $v = null) {
  if ($v !== null) { $_SESSION['_flash'][$k] = $v; return; }
  $v = $_SESSION['_flash'][$k] ?? null; unset($_SESSION['_flash'][$k]); return $v;
}
function view(string $name, array $data = []): void {
  extract($data, EXTR_SKIP);
  $base = dirname(__DIR__) . '/Views/';
  require $base . 'layout/header.php';
  require $base . $name . '.php';
  require $base . 'layout/footer.php';
}
function view_admin(string $name, array $data = []): void {
  extract($data, EXTR_SKIP);
  $base = dirname(__DIR__) . '/Views/';
  require $base . 'layout/admin_header.php';
  require $base . $name . '.php';
  require $base . 'layout/admin_footer.php';
}
function money(float $n): string { return 'RM ' . number_format($n, 2); }
function asset(string $p): string {
  // Cache-busting: URL changes whenever the file changes, so browsers never keep stale CSS/JS
  $f = dirname(__DIR__, 2) . '/public' . strtok($p, '?');
  return $p . '?v=' . (@filemtime($f) ?: 1);
}
function csrf_field(): string { return Csrf::field(); }
function require_post(): void {
  if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') { http_response_code(405); exit('Method not allowed'); }
  if (!Csrf::verify($_POST['csrf'] ?? null)) { http_response_code(419); exit('CSRF token mismatch'); }
}
function json_out($data, int $code = 200): void {
  http_response_code($code); header('Content-Type: application/json'); echo json_encode($data); exit;
}
function slugify(string $s): string {
  $s = strtolower(trim($s)); $s = preg_replace('/[^a-z0-9]+/', '-', $s); return trim($s, '-');
}
