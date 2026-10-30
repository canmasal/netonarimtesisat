<?php
require_once __DIR__ . '/helpers.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('NOPANEL');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
    session_start();
}

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');

function kullanici(): ?array
{
    static $u = false;
    if ($u !== false) return $u;
    $u = null;
    if (!empty($_SESSION['uid'])) {
        $s = db()->prepare('SELECT * FROM kullanicilar WHERE id = ? AND aktif = 1');
        $s->execute([$_SESSION['uid']]);
        $u = $s->fetch() ?: null;
        if (!$u) unset($_SESSION['uid']);
    }
    return $u;
}

function giris_gerekli(?string $rol = null): array
{
    $u = kullanici();
    if (!$u) yonlendir('giris.php');
    if ($rol !== null && $u['rol'] !== $rol) {
        http_response_code(403);
        exit('Bu sayfaya erişim yetkiniz yok.');
    }
    return $u;
}

function admin_mi(): bool
{
    return (kullanici()['rol'] ?? '') === 'admin';
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}

function csrf_alan(): string
{
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

function csrf_kontrol(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !hash_equals($_SESSION['csrf'] ?? '', (string)($_POST['csrf'] ?? ''))) {
        http_response_code(400);
        exit('Oturum süresi doldu. Lütfen sayfayı yenileyip tekrar deneyin.');
    }
}

function flash(?string $mesaj = null, string $tip = 'ok'): ?array
{
    if ($mesaj !== null) {
        $_SESSION['flash'] = [$mesaj, $tip];
        return null;
    }
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function oturum_ac(array $u): void
{
    session_regenerate_id(true);
    $_SESSION['uid'] = (int)$u['id'];
    unset($_SESSION['csrf']);
}
