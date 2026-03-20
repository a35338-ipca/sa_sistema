<?php
define('DB_HOST',    'localhost');
define('DB_NAME',    'sistema_academico');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('SITE_URL',   'http://localhost/sa_sistema');
define('UPLOAD_DIR', __DIR__ . '/../uploads/fotos/');
define('UPLOAD_URL', SITE_URL . '/uploads/fotos/');
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2 MB
define('ALLOWED_TYPES', ['image/jpeg','image/png','image/webp']);

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 3600,
        'cookie_httponly' => true,
        'use_strict_mode' => true,
    ]);
}
