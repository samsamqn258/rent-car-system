<?php
// Load environment variables from .env file
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

// Database configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'car_rental_db');
define('DB_USERNAME', getenv('DB_USERNAME') ?: 'root');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');

// MoMo Payment Configuration
define('MOMO_ENDPOINT', getenv('MOMO_ENDPOINT') ?: 'https://test-payment.momo.vn/gw_payment/transactionProcessor');
define('MOMO_PARTNER_CODE', getenv('MOMO_PARTNER_CODE') ?: 'MOMOTEST');
define('MOMO_ACCESS_KEY', getenv('MOMO_ACCESS_KEY') ?: 'F8BBA842ECF85');
define('MOMO_SECRET_KEY', getenv('MOMO_SECRET_KEY') ?: 'K951B6PE1waDMi640xX08PD3vg6EkVlz');

// Application settings
define('BASE_URL', getenv('BASE_URL') ?: 'http://localhost/car_rental_php');
define('APP_NAME', 'Mioto');
define('UPLOAD_DIR', __DIR__ . '/../public/uploads/');

// Session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
