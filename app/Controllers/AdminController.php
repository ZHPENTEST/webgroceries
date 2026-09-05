<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Auth;
use App\Core\Database;
final class AdminController {
  public static function dash(): void {
    Auth::requireAdmin(); $pdo = Database::pdo();
    $s = [
      'sales' => $pdo->query('SELECT COALESCE(SUM(total),0) s FROM orders WHERE status!="cancelled"')->fetch()['s'],
      'orders' => $pdo->query('SELECT COUNT(*) c FROM orders')->fetch()['c'],
      'customers' => $pdo->query('SELECT COUNT(*) c FROM users WHERE role="customer"')->fetch()['c'],
      'products' => $pdo->query('SELECT COUNT(*) c FROM products')->fetch()['c'],
    ];
    $low = $pdo->query('SELECT * FROM products WHERE stock_quantity <= low_stock_threshold ORDER BY stock_quantity LIMIT 8')->fetchAll();
    $pend = $pdo->query('SELECT * FROM orders WHERE status="pending" ORDER BY id DESC LIMIT 8')->fetchAll();
    $recent = $pdo->query('SELECT * FROM orders ORDER BY id DESC LIMIT 8')->fetchAll();
    $daily = $pdo->query('SELECT DATE(created_at) d, SUM(total) t FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY d ORDER BY d')->fetchAll();
    view_admin('admin/dash', ['title' => 'Dashboard'] + $s + ['low' => $low, 'pend' => $pend, 'recent' => $recent, 'daily' => $daily]);
  }
  public static function products(): void {
    Auth::requireAdmin();
    $q = trim($_GET['q'] ?? '');
    $sql = 'SELECT p.*, c.name cat FROM products p JOIN categories c ON c.id=p.category_id';
    $params = [];
    if ($q !== '') { $sql .= ' WHERE p.name LIKE ?'; $params[] = "%$q%"; }
    $sql .= ' ORDER BY p.id DESC LIMIT 100';
    $st = Database::pdo()->prepare($sql); $st->execute($params);
    $cats = Database::pdo()->query('SELECT * FROM categories ORDER BY name')->fetchAll();
    view_admin('admin/products', ['title' => 'Products', 'items' => $st->fetchAll(), 'cats' => $cats, 'q' => $q]);
  }
  public static function saveProduct(): void {
    Auth::requireAdmin(); require_post();
    $pdo = Database::pdo();
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? ''); $price = (float)($_POST['price'] ?? 0);
    $dp = $_POST['discount_price'] !== '' ? (float)$_POST['discount_price'] : null;
    if ($name === '' || $price < 0 || ($dp !== null && $dp >= $price)) { flash('error', 'Invalid product data'); redirect('/admin/products'); }
    if ($id > 0) { // ownership of slug: keep unique
      $chk = $pdo->prepare('SELECT id FROM products WHERE id=?'); $chk->execute([$id]); if (!$chk->fetch()) { http_response_code(404); exit; }
    }
    $img = trim($_POST['existing_image'] ?? '');
    if (!empty($_FILES['image']['tmp_name'])) {
      $img = self::handleUpload($_FILES['image']);
    }
    $slug = slugify($name) . '-' . substr(bin2hex(random_bytes(3)), 0, 6);
    $data = [(int)$_POST['category_id'], trim($_POST['brand'] ?? ''), $name, $slug, trim($_POST['description'] ?? ''), $price, $dp, (int)$_POST['stock_quantity'], trim($_POST['unit'] ?? 'pc'), $img ?: null, $_POST['status'] ?? 'active', isset($_POST['is_featured']) ? 1 : 0, isset($_POST['is_bestseller']) ? 1 : 0];
    if ($id > 0) {
      // keep original slug
      $pdo->prepare('UPDATE products SET category_id=?,brand=?,name=?,description=?,price=?,discount_price=?,stock_quantity=?,unit=?,image=COALESCE(?,image),status=?,is_featured=?,is_bestseller=? WHERE id=?')
        ->execute([$data[0], $data[1], $data[2], $data[4], $data[5], $data[6], $data[7], $data[8], $img ?: null, $data[10], $data[11], $data[12], $id]);
    } else {
      $pdo->prepare('INSERT INTO products (category_id,brand,name,slug,description,price,discount_price,stock_quantity,unit,image,status,is_featured,is_bestseller) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)')
        ->execute([$data[0], $data[1], $data[2], $slug, $data[4], $data[5], $data[6], $data[7], $data[8], $data[9], $data[10], $data[11], $data[12]]);
    }
    flash('ok', 'Product saved'); redirect('/admin/products');
  }
  public static function stockAdjust(int $id): void {
    Auth::requireAdmin(); require_post();
    $d = max(-50, min(50, (int)($_POST['delta'] ?? 0)));
    $pdo = Database::pdo();
    $pdo->prepare('UPDATE products SET stock_quantity = GREATEST(0, stock_quantity + ?) WHERE id=?')->execute([$d, $id]);
    $st = $pdo->prepare('SELECT stock_quantity FROM products WHERE id=?'); $st->execute([$id]);
    json_out(['ok' => true, 'qty' => (int)($st->fetch()['stock_quantity'] ?? 0)]);
  }
  public static function reviews(): void {
    Auth::requireAdmin();
    $items = Database::pdo()->query('SELECT r.*, p.name pname, u.name uname FROM reviews r JOIN products p ON p.id=r.product_id JOIN users u ON u.id=r.user_id ORDER BY r.id DESC LIMIT 100')->fetchAll();
    view_admin('admin/reviews', ['title' => 'Reviews', 'items' => $items]);
  }
  public static function payConfirm(int $id): void {
    Auth::requireAdmin(); require_post();
    $pdo = Database::pdo();
    $pdo->prepare('UPDATE payments SET status="paid" WHERE order_id=?')->execute([$id]);
    $pdo->prepare('UPDATE orders SET payment_status="paid" WHERE id=?')->execute([$id]);
    flash('ok', 'Payment confirmed'); redirect('/admin/orders/' . $id);
  }
  public static function payReject(int $id): void {
    Auth::requireAdmin(); require_post();
    Database::pdo()->prepare('UPDATE payments SET status="pending" WHERE order_id=?')->execute([$id]);
    flash('ok', 'Sent back to pending'); redirect('/admin/orders/' . $id);
  }
  public static function settings(): void {
    Auth::requireAdmin();
    $qr = is_file(dirname(__DIR__, 2) . '/public/assets/images/payment-qr.jpg') ? '/assets/images/payment-qr.jpg' : null;
    view_admin('admin/settings', ['title' => 'Settings', 'qr' => $qr, 'mapKey' => \App\Models\Settings::get('google_maps_key')]);
  }
  public static function saveMapKey(): void {
    Auth::requireAdmin(); require_post();
    $k = trim($_POST['mapkey'] ?? '');
    if (strlen($k) > 200) { flash('error', 'Key too long'); redirect('/admin/settings'); }
    \App\Models\Settings::set('google_maps_key', $k === '' ? null : $k);
    flash('ok', 'Maps key saved (kosongkan untuk guna peta percuma)'); redirect('/admin/settings');
  }
  public static function saveQr(): void {
    Auth::requireAdmin(); require_post();
    $f = $_FILES['qr'] ?? null;
    if (!$f || $f['error'] !== UPLOAD_ERR_OK || $f['size'] > 2 * 1024 * 1024) { flash('error', 'Upload failed (max 2MB)'); redirect('/admin/settings'); }
    $fi = new \finfo(FILEINFO_MIME_TYPE); $mime = $fi->file($f['tmp_name']);
    if (!in_array($mime, ['image/jpeg','image/png','image/webp'], true)) { flash('error', 'Only JPG/PNG/WebP'); redirect('/admin/settings'); }
    $dst = dirname(__DIR__, 2) . '/public/assets/images/payment-qr.jpg';
    if ($mime === 'image/jpeg') { $im = imagecreatefromjpeg($f['tmp_name']); }
    elseif ($mime === 'image/png') { $im = imagecreatefrompng($f['tmp_name']); }
    else { $im = imagecreatefromwebp($f['tmp_name']); }
    imagejpeg($im, $dst, 88);
    flash('ok', 'Payment QR updated'); redirect('/admin/settings');
  }
  public static function delReview(int $id): void {
    Auth::requireAdmin(); require_post();
    Database::pdo()->prepare('DELETE FROM reviews WHERE id=?')->execute([$id]);
    redirect('/admin/reviews');
  }
  public static function delProduct(int $id): void {
    Auth::requireAdmin(); require_post();
    Database::pdo()->prepare('DELETE FROM products WHERE id=?')->execute([$id]);
    redirect('/admin/products');
  }
  public static function categories(): void {
    Auth::requireAdmin();
    $items = Database::pdo()->query('SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id=c.id) n FROM categories c ORDER BY name')->fetchAll();
    view_admin('admin/categories', ['title' => 'Categories', 'items' => $items]);
  }
  public static function saveCategory(): void {
    Auth::requireAdmin(); require_post();
    $name = trim($_POST['name'] ?? ''); if ($name === '') { flash('error', 'Name required'); redirect('/admin/categories'); }
    $pdo = Database::pdo(); $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) $pdo->prepare('UPDATE categories SET name=?, description=?, status=? WHERE id=?')->execute([$name, trim($_POST['description'] ?? ''), $_POST['status'] ?? 'active', $id]);
    else $pdo->prepare('INSERT INTO categories (name,slug,description,status) VALUES (?,?,?,?)')->execute([$name, slugify($name) . '-' . substr(bin2hex(random_bytes(2)), 0, 4), trim($_POST['description'] ?? ''), 'active']);
    redirect('/admin/categories');
  }
  public static function delCategory(int $id): void {
    Auth::requireAdmin(); require_post();
    $pdo = Database::pdo();
    $c = $pdo->prepare('SELECT COUNT(*) n FROM products WHERE category_id=?'); $c->execute([$id]);
    if ((int)$c->fetch()['n'] > 0) { flash('error', 'Category has products — move or delete them first'); redirect('/admin/categories'); }
    $pdo->prepare('DELETE FROM categories WHERE id=?')->execute([$id]);
    redirect('/admin/categories');
  }
  public static function orders(): void {
    Auth::requireAdmin();
    $st = $_GET['status'] ?? '';
    $sql = 'SELECT * FROM orders'; $p = [];
    if ($st !== '') { $sql .= ' WHERE status=?'; $p[] = $st; }
    $sql .= ' ORDER BY id DESC LIMIT 100';
    $s = Database::pdo()->prepare($sql); $s->execute($p);
    view_admin('admin/orders', ['title' => 'Orders', 'items' => $s->fetchAll(), 'st' => $st]);
  }
  public static function orderShow(int $id): void {
    Auth::requireAdmin(); $pdo = Database::pdo();
    $st = $pdo->prepare('SELECT * FROM orders WHERE id=? LIMIT 1'); $st->execute([$id]); $o = $st->fetch();
    if (!$o) { http_response_code(404); exit('Not found'); }
    $it = $pdo->prepare('SELECT * FROM order_items WHERE order_id=?'); $it->execute([$id]);
    $py = $pdo->prepare('SELECT * FROM payments WHERE order_id=? LIMIT 1'); $py->execute([$id]);
    view_admin('admin/order_detail', ['title' => 'Order ' . $o['order_number'], 'o' => $o, 'items' => $it->fetchAll(), 'pay' => $py->fetch() ?: null]);
  }
  public static function orderStatus(int $id): void {
    Auth::requireAdmin(); require_post();
    $allowed = ['pending','confirmed','processing','packed','out_for_delivery','delivered','cancelled'];
    if (!in_array($_POST['status'] ?? '', $allowed, true)) { http_response_code(400); exit('Bad status'); }
    Database::pdo()->prepare('UPDATE orders SET status=? WHERE id=?')->execute([$_POST['status'], $id]);
    redirect('/admin/orders/' . $id);
  }
  public static function customers(): void {
    Auth::requireAdmin();
    $items = Database::pdo()->query('SELECT id,name,email,phone,role,status,created_at FROM users WHERE role="customer" ORDER BY id DESC LIMIT 100')->fetchAll();
    view_admin('admin/customers', ['title' => 'Customers', 'items' => $items]);
  }
  public static function coupons(): void {
    Auth::requireAdmin();
    $items = Database::pdo()->query('SELECT * FROM coupons ORDER BY id DESC')->fetchAll();
    view_admin('admin/coupons', ['title' => 'Coupons', 'items' => $items]);
  }
  public static function saveCoupon(): void {
    Auth::requireAdmin(); require_post();
    Database::pdo()->prepare('INSERT INTO coupons (code,type,value,min_subtotal,status,expires_at) VALUES (?,?,?,?,?,?)')
      ->execute([strtoupper(trim($_POST['code'])), $_POST['type'] ?? 'percent', (float)$_POST['value'], (float)($_POST['min_subtotal'] ?? 0), 'active', $_POST['expires_at'] ?: null]);
    redirect('/admin/coupons');
  }
  private static function handleUpload(array $f): string {
    if ($f['error'] !== UPLOAD_ERR_OK) throw new \RuntimeException('Upload failed');
    if ($f['size'] > 2 * 1024 * 1024) throw new \RuntimeException('Max 2MB');
    $fi = new \finfo(FILEINFO_MIME_TYPE); $mime = $fi->file($f['tmp_name']);
    if (!in_array($mime, ['image/jpeg','image/png','image/webp'], true)) throw new \RuntimeException('Only JPG/PNG/WebP');
    $ext = ['image/jpeg' => 'jpg','image/png' => 'png','image/webp' => 'webp'][$mime];
    $dims = getimagesize($f['tmp_name']);
    if (!$dims || $dims[0] < 50 || $dims[1] < 50 || $dims[0] > 4000) throw new \RuntimeException('Invalid dimensions');
    $name = bin2hex(random_bytes(12)) . '.' . $ext;
    $dir = dirname(__DIR__, 2) . '/public/assets/images/products';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    // strip metadata by re-encoding
    $dst = $dir . '/' . $name;
    if ($mime === 'image/jpeg') { $im = imagecreatefromjpeg($f['tmp_name']); imagejpeg($im, $dst, 85); }
    elseif ($mime === 'image/png') { $im = imagecreatefrompng($f['tmp_name']); imagepng($im, $dst, 6); }
    else { $im = imagecreatefromwebp($f['tmp_name']); imagewebp($im, $dst, 85); }
    $twin = preg_replace('/\.(jpg|png)$/', '.webp', $dst);
    if ($twin !== $dst) imagewebp($im, $twin, 80);
    return '/assets/images/products/' . $name;
  }
}
