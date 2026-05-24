<?php
require_once __DIR__ . '/config.php';
session_start();

function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}
function current_user(): ?array {
    return is_logged_in() ? [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'] ?? null,
        'role' => $_SESSION['user_role'] ?? null
    ] : null;
}
function require_login(): void {
    if (!is_logged_in()) {
        header('Location: /login.php');
        exit;
    }
}
function require_admin(): void {
    require_login();
    $u = current_user();
    if (!$u || ($u['role'] ?? '') !== 'admin') {
        http_response_code(403);
        echo "Bạn không có quyền truy cập.";
        exit;
    }
}
function flash_set(string $key, string $msg): void {
    $_SESSION['flash'][$key] = $msg;
}
function flash_get(string $key): ?string {
    $v = $_SESSION['flash'][$key] ?? null;
    if ($v) unset($_SESSION['flash'][$key]);
    return $v;
}
