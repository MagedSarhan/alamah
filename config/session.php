<?php
/**
 * علامة | ALAMAH — Session Configuration
 */

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

/**
 * Generate or get CSRF token
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Output hidden CSRF field
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/**
 * Verify CSRF token
 */
function verify_csrf(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Set flash message
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Get and clear flash message
 */
function get_flash(): ?array {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

/**
 * Check if user is logged in
 */
function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function is_admin(): bool {
    return !empty($_SESSION['is_admin']);
}

/**
 * Get current user ID
 */
function current_user_id(): ?int {
    return $_SESSION['user_id'] ?? null;
}
