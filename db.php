<?php
require_once __DIR__ . '/config.php';

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4";
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die('<div style="font-family:sans-serif;max-width:600px;margin:4rem auto;padding:2rem;background:#fff0f0;border:1px solid #c00;border-radius:12px">
                <h2 style="color:#c00">⚠️ Erro de ligação à base de dados</h2>
                <p style="margin:.75rem 0">Verifique se o XAMPP está a correr e se importou o ficheiro <code>database.sql</code>.</p>
                <code style="display:block;padding:1rem;background:#fff;border-radius:8px;font-size:.85rem">'
                .htmlspecialchars($e->getMessage()).'</code></div>');
        }
    }
    return $pdo;
}
