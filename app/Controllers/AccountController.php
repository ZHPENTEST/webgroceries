<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Auth;
use App\Core\Database;
final class AccountController {
  public static function dashboard(): void {
    Auth::requireLogin(); $u = Auth::user();
    $pdo = Database::pdo();
    $orders = $pdo->prepare('SELECT * FROM orders WHERE user_id=? ORDER BY id DESC LIMIT 5'); $orders->execute([$u['id']]);
    $addrs = $pdo->prepare('SELECT * FROM addresses WHERE user_id=?'); $addrs->execute([$u['id']]);
    $wl = $pdo->prepare('SELECT p.* FROM wishlists w JOIN products p ON p.id=w.product_id WHERE w.user_id=?'); $wl->execute([$u['id']]);
    view('account/dashboard', ['title' => 'My account', 'u' => $u, 'orders' => $orders->fetchAll(), 'addrs' => $addrs->fetchAll(), 'wl' => $wl->fetchAll()]);
  }
  public static function updateProfile(): void {
    Auth::requireLogin(); require_post();
    $name = trim($_POST['name'] ?? ''); $phone = trim($_POST['phone'] ?? '');
    if (strlen($name) < 2) { flash('error', 'Invalid name'); redirect('/account'); }
    Database::pdo()->prepare('UPDATE users SET name=?, phone=? WHERE id=?')->execute([$name, $phone, $_SESSION['uid']]);
    flash('ok', 'Profile updated'); redirect('/account');
  }
  public static function changePassword(): void {
    Auth::requireLogin(); require_post();
    $pdo = Database::pdo();
    $st = $pdo->prepare('SELECT password_hash FROM users WHERE id=?'); $st->execute([$_SESSION['uid']]);
    $cur = $st->fetch()['password_hash'];
    if (!password_verify($_POST['current'] ?? '', $cur) || strlen($_POST['new'] ?? '') < 8) {
      flash('error', 'Password change failed'); redirect('/account'); }
    $pdo->prepare('UPDATE users SET password_hash=? WHERE id=?')->execute([password_hash($_POST['new'], PASSWORD_DEFAULT), $_SESSION['uid']]);
    flash('ok', 'Password changed'); redirect('/account');
  }
  public static function saveAddress(): void {
    Auth::requireLogin(); require_post();
    $pdo = Database::pdo();
    // IDOR guard: verify ownership when editing
    if (!empty($_POST['id'])) {
      $chk = $pdo->prepare('SELECT id FROM addresses WHERE id=? AND user_id=?'); $chk->execute([(int)$_POST['id'], $_SESSION['uid']]);
      if (!$chk->fetch()) { http_response_code(403); exit('Forbidden'); }
      $pdo->prepare('UPDATE addresses SET label=?,recipient=?,phone=?,line1=?,city=?,postcode=? WHERE id=?')->execute(
        [$_POST['label'] ?? 'Home', $_POST['recipient'], $_POST['phone'], $_POST['line1'], $_POST['city'], $_POST['postcode'], (int)$_POST['id']]);
    } else {
      $pdo->prepare('INSERT INTO addresses (user_id,label,recipient,phone,line1,city,postcode,is_default) VALUES (?,?,?,?,?,?,?,?)')->execute(
        [$_SESSION['uid'], $_POST['label'] ?? 'Home', $_POST['recipient'], $_POST['phone'], $_POST['line1'], $_POST['city'], $_POST['postcode'], 0]);
    }
    flash('ok', 'Address saved'); redirect('/account');
  }
  public static function delAddress(int $id): void {
    Auth::requireLogin(); require_post();
    Database::pdo()->prepare('DELETE FROM addresses WHERE id=? AND user_id=?')->execute([$id, $_SESSION['uid']]);
    redirect('/account');
  }
  public static function uploadAvatar(): void {
    Auth::requireLogin(); require_post();
    if (empty($_FILES['avatar']['tmp_name'])) { flash('error', 'Choose a photo first'); redirect('/account'); }
    $f = $_FILES['avatar'];
    if ($f['error'] !== UPLOAD_ERR_OK || $f['size'] > 2 * 1024 * 1024) { flash('error', 'Upload failed (max 2MB)'); redirect('/account'); }
    $fi = new \finfo(FILEINFO_MIME_TYPE); $mime = $fi->file($f['tmp_name']);
    if (!in_array($mime, ['image/jpeg','image/png','image/webp'], true)) { flash('error', 'Only JPG/PNG/WebP'); redirect('/account'); }
    $dims = getimagesize($f['tmp_name']);
    if (!$dims || $dims[0] < 50 || $dims[1] < 50) { flash('error', 'Invalid image'); redirect('/account'); }
    $ext = ['image/jpeg' => 'jpg','image/png' => 'png','image/webp' => 'webp'][$mime];
    $name = 'u' . (int)$_SESSION['uid'] . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $dir = dirname(__DIR__, 2) . '/public/assets/images/avatars';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $dst = $dir . '/' . $name;
    if ($mime === 'image/jpeg') { $im = imagecreatefromjpeg($f['tmp_name']); imagejpeg($im, $dst, 85); }
    elseif ($mime === 'image/png') { $im = imagecreatefrompng($f['tmp_name']); imagepng($im, $dst, 6); }
    else { $im = imagecreatefromwebp($f['tmp_name']); imagewebp($im, $dst, 85); }
    $pdo = Database::pdo();
    $old = $pdo->prepare('SELECT avatar FROM users WHERE id=?'); $old->execute([$_SESSION['uid']]);
    if ($prev = $old->fetch()['avatar'] ?? null) { @unlink(dirname(__DIR__, 2) . '/public' . $prev); }
    $pdo->prepare('UPDATE users SET avatar=? WHERE id=?')->execute(['/assets/images/avatars/' . $name, $_SESSION['uid']]);
    flash('ok', 'Profile photo updated'); redirect('/account');
  }
  public static function wishlistToggle(): void {
    Auth::requireLogin();
    $pid = (int)($_POST['id'] ?? 0);
    $pdo = Database::pdo();
    $ex = $pdo->prepare('SELECT 1 FROM wishlists WHERE user_id=? AND product_id=?'); $ex->execute([$_SESSION['uid'], $pid]);
    if ($ex->fetch()) $pdo->prepare('DELETE FROM wishlists WHERE user_id=? AND product_id=?')->execute([$_SESSION['uid'], $pid]);
    else $pdo->prepare('INSERT IGNORE INTO wishlists VALUES (?,?,NOW())')->execute([$_SESSION['uid'], $pid]);
    if (($_SERVER['HTTP_ACCEPT'] ?? '') === 'application/json' || isset($_POST['ajax'])) json_out(['ok' => true]);
    redirect_back('/account');
  }
}
