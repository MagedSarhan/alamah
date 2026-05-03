<?php
/**
 * علامة | ALAMAH — Auth Middleware
 * Include this at the top of any admin page to protect it.
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/functions.php';

if (!is_logged_in() || !is_admin()) {
    header('Location: ' . dirname($_SERVER['SCRIPT_NAME'], 2) . '/auth/login.php');
    exit;
}
