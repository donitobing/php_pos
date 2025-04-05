<?php
session_start();

// Load Config
require_once '../config/config.php';

// Load System Files
require_once '../system/Core.php';
require_once '../system/Controller.php';
require_once '../system/Database.php';

// Autoload Core Libraries
spl_autoload_register(function($className) {
    require_once '../app/libraries/' . $className . '.php';
});

// Security Headers
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header("Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval'; img-src 'self' data:;");

// Initialize Core
$init = new Core();
