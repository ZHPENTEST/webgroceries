<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Database;
final class Settings {
  public static function get(string $k, ?string $d = null): ?string {
    $st = Database::pdo()->prepare('SELECT `v` FROM site_settings WHERE `k`=? LIMIT 1');
    $st->execute([$k]); $r = $st->fetch();
    return $r ? (string)$r['v'] : $d;
  }
  public static function set(string $k, ?string $v): void {
    Database::pdo()->prepare('INSERT INTO site_settings (`k`,`v`) VALUES (?,?) ON DUPLICATE KEY UPDATE `v`=VALUES(`v`)')->execute([$k, $v]);
  }
}
