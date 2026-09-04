<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Database;
use App\Models\Product;
final class ShopController {
  public static function shop(): void {
    $page = max(1, (int)($_GET['page'] ?? 1));
    $f = ['q' => trim($_GET['q'] ?? ''), 'category' => trim($_GET['cat'] ?? ''), 'min' => $_GET['min'] ?? '', 'max' => $_GET['max'] ?? '', 'sort' => $_GET['sort'] ?? 'new'];
    $r = Product::list($f, $page, 12);
    $cats = Database::pdo()->query('SELECT * FROM categories WHERE status="active" ORDER BY name')->fetchAll();
    view('shop', ['title' => 'Shop groceries', 'items' => $r['items'], 'total' => $r['total'], 'pages' => $r['pages'], 'page' => $page, 'cats' => $cats, 'f' => $f]);
  }
  public static function category(string $slug): void {
    $st = Database::pdo()->prepare('SELECT * FROM categories WHERE slug=? AND status="active" LIMIT 1');
    $st->execute([$slug]); $cat = $st->fetch();
    if (!$cat) { http_response_code(404); view('errors/404', ['title' => 'Not found']); return; }
    $page = max(1, (int)($_GET['page'] ?? 1));
    $r = Product::list(['category' => $slug, 'sort' => $_GET['sort'] ?? 'new'], $page, 12);
    $cats = Database::pdo()->query('SELECT * FROM categories WHERE status="active" ORDER BY name')->fetchAll();
    view('shop', ['title' => $cat['name'], 'items' => $r['items'], 'total' => $r['total'], 'pages' => $r['pages'], 'page' => $page, 'cats' => $cats, 'f' => ['cat' => $slug, 'q' => '', 'sort' => $_GET['sort'] ?? 'new'], 'heading' => $cat['name']]);
  }
  public static function product(string $slug): void {
    $p = Product::bySlug($slug);
    if (!$p || $p['status'] !== 'active') { http_response_code(404); view('errors/404', ['title' => 'Not found']); return; }
    $pdo = Database::pdo();
    $ag = $pdo->prepare('SELECT COUNT(*) n, COALESCE(AVG(rating),0) a FROM reviews WHERE product_id=? AND status="approved"');
    $ag->execute([$p['id']]); $agg = $ag->fetch();
    $rl = $pdo->prepare('SELECT r.*, u.name uname FROM reviews r JOIN users u ON u.id=r.user_id WHERE r.product_id=? AND r.status="approved" ORDER BY r.id DESC LIMIT 20');
    $rl->execute([$p['id']]);
    $can = false; $mine = null;
    if (isset($_SESSION['uid'])) {
      $b = $pdo->prepare('SELECT 1 FROM orders o JOIN order_items i ON i.order_id=o.id WHERE o.user_id=? AND i.product_id=? AND o.status!="cancelled" LIMIT 1');
      $b->execute([$_SESSION['uid'], $p['id']]); $can = (bool)$b->fetch();
      $m = $pdo->prepare('SELECT * FROM reviews WHERE product_id=? AND user_id=? LIMIT 1');
      $m->execute([$p['id'], $_SESSION['uid']]); $mine = $m->fetch() ?: null;
    }
    // Recently viewed (session)
    $_SESSION['recent'] = array_values(array_diff(array_merge([$slug], $_SESSION['recent'] ?? []), [''])) ;
    $_SESSION['recent'] = array_slice(array_unique($_SESSION['recent']), 0, 8);
    $recent = [];
    $others = array_values(array_filter($_SESSION['recent'], fn($s) => $s !== $slug));
    if ($others) {
      $in = implode(',', array_fill(0, count($others), '?'));
      $st = $pdo->prepare("SELECT * FROM products WHERE slug IN ($in) AND status='active' LIMIT 4");
      $st->execute($others); $recent = $st->fetchAll();
    }
    view('product', ['title' => $p['name'], 'p' => $p, 'related' => Product::related($p), 'agg' => $agg, 'reviews' => $rl->fetchAll(), 'can' => $can, 'mine' => $mine, 'recent' => $recent]);
  }
  public static function review(string $slug): void {
    \App\Core\Auth::requireLogin(); require_post();
    $pdo = Database::pdo();
    $st = $pdo->prepare('SELECT id FROM products WHERE slug=? LIMIT 1'); $st->execute([$slug]);
    $pr = $st->fetch();
    if (!$pr) { http_response_code(404); exit('No product'); }
    $b = $pdo->prepare('SELECT 1 FROM orders o JOIN order_items i ON i.order_id=o.id WHERE o.user_id=? AND i.product_id=? AND o.status!="cancelled" LIMIT 1');
    $b->execute([$_SESSION['uid'], $pr['id']]);
    if (!$b->fetch()) { flash('error', 'Only verified buyers can review'); redirect('/product/' . $slug); }
    $r = max(1, min(5, (int)($_POST['rating'] ?? 5)));
    $title = mb_substr(trim($_POST['title'] ?? ''), 0, 120);
    $body = mb_substr(trim($_POST['body'] ?? ''), 0, 1000);
    if (mb_strlen($body) < 3) { flash('error', 'Review too short'); redirect('/product/' . $slug); }
    $pdo->prepare('INSERT INTO reviews (product_id,user_id,rating,title,body) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE rating=VALUES(rating),title=VALUES(title),body=VALUES(body),status="approved"')
      ->execute([$pr['id'], $_SESSION['uid'], $r, $title ?: null, $body]);
    flash('ok', 'Thanks for your review'); redirect('/product/' . $slug);
  }
}
