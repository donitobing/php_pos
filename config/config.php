<?php
define('BASE_URL', 'http://localhost/pointofsales/');
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pos_db');

// Application settings
define('APP_NAME', 'Modern POS System');
define('APP_VERSION', '1.0.0');
define('ENCRYPTION_KEY', bin2hex(random_bytes(32))); // For session and data encryption
define('DEFAULT_CONTROLLER', 'Auth');
define('DEFAULT_ACTION', 'index');

// Session settings
define('SESSION_LIFETIME', 3600); // 1 hour
define('CSRF_TOKEN_NAME', 'csrf_token');

// Audit trail settings
define('ENABLE_AUDIT_TRAIL', true);

// Tax settings
define('DEFAULT_TAX_RATE', 10); // 10%
