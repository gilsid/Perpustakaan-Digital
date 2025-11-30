<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Base paths for filesystem
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/..');
}
if (!defined('DATA_PATH')) {
    define('DATA_PATH', BASE_PATH . '/data');
}
if (!defined('UPLOADS_PATH')) {
    define('UPLOADS_PATH', BASE_PATH . '/uploads');
}

// Simple timezone and encoding defaults
if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set('Asia/Jakarta'); // WIB (UTC+7)
}

require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/includes/auth.php';
