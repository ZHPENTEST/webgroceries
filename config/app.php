<?php
declare(strict_types=1);
return [
  'name' => 'WebGroceries',
  'url' => $_ENV['APP_URL'] ?? 'http://localhost:8000',
  'env' => $_ENV['APP_ENV'] ?? 'dev',
  'delivery_fees' => ['standard' => 4.90, 'express' => 9.90, 'scheduled' => 6.90],
  'free_shipping_over' => 80.00,
  'upload_dir' => dirname(__DIR__) . '/public/assets/images/products',
  'upload_max_bytes' => 2 * 1024 * 1024,
];
