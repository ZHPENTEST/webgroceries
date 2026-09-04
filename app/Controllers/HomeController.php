<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Database;
use App\Models\Product;
final class HomeController {
  public static function index(): void {
    $pdo = Database::pdo();
    $featured = $pdo->query('SELECT * FROM products WHERE status="active" AND is_featured=1 ORDER BY id DESC LIMIT 8')->fetchAll();
    $best = $pdo->query('SELECT * FROM products WHERE status="active" AND is_bestseller=1 ORDER BY id DESC LIMIT 8')->fetchAll();
    $deals = $pdo->query('SELECT * FROM products WHERE status="active" AND discount_price IS NOT NULL ORDER BY (price-discount_price) DESC LIMIT 4')->fetchAll();
    $cats = $pdo->query('SELECT * FROM categories WHERE status="active" LIMIT 9')->fetchAll();
    $flashEnds = date('c', strtotime('next sunday 23:59:59'));
    $reviews = [
      ['n' => 'Aina R.', 't' => 'Groceries arrived chilled and fast. Strawberries were perfect.', 'r' => 5],
      ['n' => 'Daniel K.', 't' => 'Checkout took under a minute. Express delivery is worth it.', 'r' => 5],
      ['n' => 'Mei L.', 't' => 'Fresh milk and veggies are consistently good quality.', 'r' => 4],
    ];
    view('home', ['title' => 'WebGroceries — Fresh groceries delivered fast', 'featured' => $featured, 'best' => $best, 'deals' => $deals, 'cats' => $cats, 'reviews' => $reviews, 'flashEnds' => $flashEnds]);
  }
}
