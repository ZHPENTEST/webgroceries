<?php
declare(strict_types=1);
// ---- Hardened session ----
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax', 'secure' => $isHttps]);
session_start();
// Idle timeout (60 min)
if (isset($_SESSION['uid']) && isset($_SESSION['last_seen']) && time() - $_SESSION['last_seen'] > 3600) {
  session_unset(); session_destroy(); session_start();
}
$_SESSION['last_seen'] = time();
// ---- Security headers ----
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
header("Content-Security-Policy: default-src 'self'; img-src 'self' https: data:; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; frame-ancestors 'deny'; base-uri 'self'; form-action 'self'; object-src 'none'");
header_remove('X-Powered-By');
require dirname(__DIR__) . '/config/database.php';
env_load(dirname(__DIR__));
require dirname(__DIR__) . '/app/Core/Database.php';
require dirname(__DIR__) . '/app/Core/Auth.php';
require dirname(__DIR__) . '/app/Core/Csrf.php';
require dirname(__DIR__) . '/app/Core/helpers.php';
foreach (glob(dirname(__DIR__) . '/app/Models/*.php') as $f) require $f;
foreach (glob(dirname(__DIR__) . '/app/Controllers/*.php') as $f) require $f;

use App\Controllers as C;
use App\Core\Auth;

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$path = rtrim($path, '/') ?: '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
  if ($path === '/') C\HomeController::index();
  elseif ($path === '/shop') C\ShopController::shop();
  elseif (preg_match('#^/category/([\w-]+)$#', $path, $m)) C\ShopController::category($m[1]);
  elseif (preg_match('#^/product/([\w-]+)$#', $path, $m)) C\ShopController::product($m[1]);
  elseif (preg_match('#^/product/([\w-]+)/review$#', $path, $m) && $method === 'POST') C\ShopController::review($m[1]);
  elseif ($path === '/cart') C\CartController::page();
  elseif (preg_match('#^/api/cart/(\w+)$#', $path, $m)) C\CartController::api($m[1]);
  elseif ($path === '/checkout' && $method === 'GET') C\CheckoutController::show();
  elseif ($path === '/checkout/place' && $method === 'POST') C\CheckoutController::place();
  elseif ($path === '/register' && $method === 'GET') C\AuthController::registerForm();
  elseif ($path === '/register' && $method === 'POST') C\AuthController::register();
  elseif ($path === '/login' && $method === 'GET') C\AuthController::loginForm();
  elseif ($path === '/login' && $method === 'POST') C\AuthController::login();
  elseif ($path === '/logout' && $method === 'POST') C\AuthController::logout();
  elseif ($path === '/orders' && $method === 'GET') C\OrderController::index();
  elseif (preg_match('#^/orders/(\d+)$#', $path, $m)) C\OrderController::show((int)$m[1]);
  elseif (preg_match('#^/orders/(\d+)/cancel$#', $path, $m)) C\OrderController::cancel((int)$m[1]);
  elseif (preg_match('#^/orders/(\d+)/reorder$#', $path, $m)) C\OrderController::reorder((int)$m[1]);
  elseif (preg_match('#^/orders/(\d+)/claim-paid$#', $path, $m)) C\OrderController::claimPaid((int)$m[1]);
  elseif ($path === '/account') C\AccountController::dashboard();
  elseif ($path === '/account/profile' && $method === 'POST') C\AccountController::updateProfile();
  elseif ($path === '/account/password' && $method === 'POST') C\AccountController::changePassword();
  elseif ($path === '/account/avatar' && $method === 'POST') C\AccountController::uploadAvatar();
  elseif ($path === '/account/address' && $method === 'POST') C\AccountController::saveAddress();
  elseif (preg_match('#^/account/address/(\d+)/delete$#', $path, $m)) C\AccountController::delAddress((int)$m[1]);
  elseif ($path === '/wishlist' && $method === 'POST') C\AccountController::wishlistToggle();
  elseif ($path === '/admin') C\AdminController::dash();
  elseif ($path === '/admin/products') C\AdminController::products();
  elseif ($path === '/admin/products/save') C\AdminController::saveProduct();
  elseif (preg_match('#^/admin/products/(\d+)/delete$#', $path, $m)) C\AdminController::delProduct((int)$m[1]);
  elseif ($path === '/admin/categories') C\AdminController::categories();
  elseif ($path === '/admin/categories/save') C\AdminController::saveCategory();
  elseif (preg_match('#^/admin/categories/(\d+)/delete$#', $path, $m)) C\AdminController::delCategory((int)$m[1]);
  elseif ($path === '/admin/orders') C\AdminController::orders();
  elseif (preg_match('#^/admin/orders/(\d+)$#', $path, $m)) C\AdminController::orderShow((int)$m[1]);
  elseif (preg_match('#^/admin/orders/(\d+)/status$#', $path, $m)) C\AdminController::orderStatus((int)$m[1]);
  elseif (preg_match('#^/admin/orders/(\d+)/pay-confirm$#', $path, $m)) C\AdminController::payConfirm((int)$m[1]);
  elseif (preg_match('#^/admin/orders/(\d+)/pay-reject$#', $path, $m)) C\AdminController::payReject((int)$m[1]);
  elseif ($path === '/admin/settings') C\AdminController::settings();
  elseif ($path === '/admin/settings/qr') C\AdminController::saveQr();
  elseif ($path === '/admin/customers') C\AdminController::customers();
  elseif ($path === '/admin/coupons') C\AdminController::coupons();
  elseif ($path === '/admin/coupons/save') C\AdminController::saveCoupon();
  elseif ($path === '/admin/reviews') C\AdminController::reviews();
  elseif (preg_match('#^/admin/reviews/(\d+)/delete$#', $path, $m)) C\AdminController::delReview((int)$m[1]);
  elseif (preg_match('#^/admin/products/(\d+)/stock$#', $path, $m)) C\AdminController::stockAdjust((int)$m[1]);
  else { http_response_code(404); view('errors/404', ['title' => 'Page not found']); }
} catch (Throwable $e) {
  if (($_ENV['APP_ENV'] ?? 'dev') === 'dev') { http_response_code(500); echo '<pre>' . htmlspecialchars($e->getMessage() . "\n" . $e->getTraceAsString()) . '</pre>'; }
  else { @file_put_contents(dirname(__DIR__) . '/storage/logs/app.log', date('c') . ' ' . $e->getMessage() . PHP_EOL, FILE_APPEND); http_response_code(500); view('errors/500', ['title' => 'Error']); }
}
