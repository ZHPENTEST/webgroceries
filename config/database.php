<?php
declare(strict_types=1);
function env_load(string $dir): void {
  $f = $dir . '/.env';
  if (!is_file($f)) { $f = $dir . '/.env.example'; }
  if (!is_file($f)) return;
  foreach (file($f, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '#')) continue;
    [$k, $v] = array_pad(explode('=', $line, 2), 2, '');
    $_ENV[trim($k)] = trim($v);
  }
}
