<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Database;
final class Product {
  public static function effectivePrice(array $p): float {
    if ($p['discount_price'] !== null && (float)$p['discount_price'] < (float)$p['price']) return (float)$p['discount_price'];
    return (float)$p['price'];
  }
  public static function list(array $f = [], int $page = 1, int $per = 12): array {
    $pdo = Database::pdo();
    $where = ['p.status = "active"']; $params = [];
    if (!empty($f['category'])) { $where[] = 'c.slug = ?'; $params[] = $f['category']; }
    if (!empty($f['q'])) { $where[] = 'MATCH(p.name,p.description,p.brand) AGAINST(? IN NATURAL LANGUAGE MODE)'; $params[] = $f['q']; }
    if (!empty($f['min'])) { $where[] = 'COALESCE(p.discount_price,p.price) >= ?'; $params[] = (float)$f['min']; }
    if (!empty($f['max'])) { $where[] = 'COALESCE(p.discount_price,p.price) <= ?'; $params[] = (float)$f['max']; }
    $sort = match ($f['sort'] ?? 'new') {
      'price_asc' => 'COALESCE(p.discount_price,p.price) ASC',
      'price_desc' => 'COALESCE(p.discount_price,p.price) DESC',
      'name' => 'p.name ASC', default => 'p.id DESC',
    };
    $w = 'WHERE ' . implode(' AND ', $where);
    $cnt = $pdo->prepare("SELECT COUNT(*) c FROM products p JOIN categories c ON c.id=p.category_id $w");
    $cnt->execute($params); $total = (int)$cnt->fetch()['c'];
    $off = ($page - 1) * $per;
    $st = $pdo->prepare("SELECT p.*, c.name cat_name, c.slug cat_slug FROM products p JOIN categories c ON c.id=p.category_id $w ORDER BY $sort LIMIT $per OFFSET $off");
    $st->execute($params);
    return ['items' => $st->fetchAll(), 'total' => $total, 'pages' => max(1, (int)ceil($total / $per))];
  }
  public static function bySlug(string $slug): ?array {
    $st = Database::pdo()->prepare('SELECT p.*, c.name cat_name, c.slug cat_slug FROM products p JOIN categories c ON c.id=p.category_id WHERE p.slug=? LIMIT 1');
    $st->execute([$slug]); return $st->fetch() ?: null;
  }
  public static function byId(int $id): ?array {
    $st = Database::pdo()->prepare('SELECT * FROM products WHERE id=? LIMIT 1');
    $st->execute([$id]); return $st->fetch() ?: null;
  }
  public static function related(array $p, int $n = 4): array {
    $st = Database::pdo()->prepare('SELECT * FROM products WHERE category_id=? AND id!=? AND status="active" LIMIT ' . $n);
    $st->execute([$p['category_id'], $p['id']]); return $st->fetchAll();
  }
}
