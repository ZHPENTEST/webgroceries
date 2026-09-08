#!/usr/bin/env php
<?php
// Weekly subscription order generator. Cron harian disyorkan:
//   0 6 * * * /usr/bin/php /var/www/groceries/cron/weekly.php >> /var/www/groceries/storage/logs/cron.log 2>&1
declare(strict_types=1);
$root = dirname(__DIR__);
require $root . '/config/database.php';
env_load($root);
require $root . '/app/Core/Database.php';
require $root . '/app/Models/Subscription.php';
use App\Core\Database;
use App\Models\Subscription;
$due = Database::pdo()->query('SELECT id FROM subscriptions WHERE status="active" AND next_run <= CURDATE()')->fetchAll();
echo date('c') . ' due: ' . count($due) . PHP_EOL;
foreach ($due as $d) {
  try {
    $r = Subscription::generate((int)$d['id']);
    echo '  OK sub ' . $d['id'] . ' -> ' . $r['order_number'] . PHP_EOL;
  } catch (Throwable $e) { echo '  FAIL sub ' . $d['id'] . ': ' . $e->getMessage() . PHP_EOL; }
}
