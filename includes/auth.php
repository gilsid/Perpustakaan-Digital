<?php

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function login_redirect_path(): string
{
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    if (strpos($script, '/admin/') !== false) {
        return '../login.php';
    }
    return 'login.php';
}

function require_login(): void
{
    if (!current_user()) {
        set_flash('warning', 'You must be logged in to access that page.');
        redirect(login_redirect_path());
    }
}

function require_admin(): void
{
    $user = current_user();
    if (!$user || empty($user['is_admin'])) {
        set_flash('danger', 'Administrator access required.');
        redirect(login_redirect_path());
    }
}

// Require regular user (not admin)
function require_regular_user(): void
{
    $user = current_user();
    if (!$user) {
        set_flash('warning', 'You must be logged in to access that page.');
        redirect(login_redirect_path());
    }
    if (!empty($user['is_admin'])) {
        set_flash('danger', 'Admin tidak dapat mengakses fitur ini.');
        redirect(login_redirect_path());
    }
}

function login_user(array $user): void
{
    $_SESSION['user'] = [
        'id'       => $user['id'],
        'name'     => $user['name'],
        'email'    => $user['email'],
        'is_admin' => !empty($user['is_admin']),
    ];
    session_regenerate_id(true);
}

function logout_user(): void
{
    unset($_SESSION['user']);
    session_regenerate_id(true);
}
