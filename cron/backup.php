#!/usr/bin/env php
<?php
// Daily database backup (pure PHP, no mysqldump needed).
// cPanel cron harian: 0 3 * * * /usr/bin/php /home/USER/grocer/cron/backup.php
// Simpan 7 terkini sahaja.
declare(strict_types=1);
$root = dirname(__DIR__);
require $root . '/config/database.php';
env_load($root);
require $root . '/app/Core/Database.php';
use App\Core\Database;
$dir = $root . '/storage/backups';
if (!is_dir($dir)) mkdir($dir, 0755, true);
$pdo = Database::pdo();
$file = $dir . '/backup-' . date('Ymd-His') . '.sql';
$out = fopen($file, 'w');
fwrite($out, '-- WebGroceries backup ' . date('c') . "\nSET FOREIGN_KEY_CHECKS=0;\n\n");
foreach ($pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN) as $t) {
  $c = $pdo->query("SHOW CREATE TABLE `$t`")->fetch();
  fwrite($out, "DROP TABLE IF EXISTS `$t`;\n" . $c['Create Table'] . ";\n\n");
  $rows = $pdo->query("SELECT * FROM `$t`");
  $cols = [];
  for ($i = 0; $i < $rows->columnCount(); $i++) { $m = $rows->getColumnMeta($i); $cols[] = '`' . $m['name'] . '`'; }
  $batch = [];
  $flush = function () use (&$batch, $out, $t, $cols) {
    if ($batch) fwrite($out, 'INSERT INTO `' . $t . '` (' . implode(',', $cols) . ') VALUES ' . implode(',', $batch) . ";\n");
    $batch = [];
  };
  foreach ($rows as $r) {
    $batch[] = '(' . implode(',', array_map(fn($v) => $v === null ? 'NULL' : $pdo->quote((string)$v), array_values($r))) . ')';
    if (count($batch) >= 200) $flush();
  }
  $flush();
  fwrite($out, "\n");
}
fclose($out);
$fs = glob($dir . '/backup-*.sql') ?: [];
rsort($fs);
foreach (array_slice($fs, 7) as $old) @unlink($old);
echo 'wrote ' . basename($file) . ' (' . round(filesize($file) / 1024) . " KB), kept " . min(7, count($fs)) . PHP_EOL;
