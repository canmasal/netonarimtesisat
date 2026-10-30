<?php
require __DIR__ . '/../inc/auth.php';

if (hash_equals($_SESSION['csrf'] ?? '', (string)($_GET['t'] ?? ''))) {
    $_SESSION = [];
    session_regenerate_id(true);
    session_destroy();
}
yonlendir('giris.php');
