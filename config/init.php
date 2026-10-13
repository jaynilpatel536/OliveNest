<?php
// Start session everywhere
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Absolute base path (NO MORE ../ confusion)
define('BASE_PATH', dirname(__DIR__));

// Includes - db.php now handles vendor/autoload.php internally
require_once BASE_PATH . '/config/db.php';
