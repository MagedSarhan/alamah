<?php
/**
 * علامة | ALAMAH — Helper Functions
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/session.php';

/**
 * Sanitize input
 */
function clean(string $str): string {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to URL
 */
function redirect(string $url): void {
    header("Location: $url");
    exit;
}

/**
 * Get base URL
 */
function base_url(string $path = ''): string {
    return SITE_URL . '/' . ltrim($path, '/');
}

/**
 * Upload image file
 */
function upload_image(array $file, string $dir): ?string {
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    if ($file['size'] > UPLOAD_MAX_SIZE) return null;
    if (!in_array($file['type'], UPLOAD_ALLOWED_TYPES)) return null;

    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('img_', true) . '.' . $ext;
    $target = $dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $target)) {
        return $filename;
    }
    return null;
}

/**
 * Format price with SAR
 */
function format_price(float $price): string {
    return number_format($price, 0) . ' <span class="sar-icon"><img src="' . base_url('image/Saudi_Riyal_Symbol.svg') . '" alt="ر.س"></span>';
}

/**
 * Time ago in Arabic
 */
function time_ago(string $datetime): string {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->y > 0) return "منذ {$diff->y} سنة";
    if ($diff->m > 0) return "منذ {$diff->m} شهر";
    if ($diff->d > 0) return "منذ {$diff->d} يوم";
    if ($diff->h > 0) return "منذ {$diff->h} ساعة";
    if ($diff->i > 0) return "منذ {$diff->i} دقيقة";
    return "الآن";
}

/**
 * Order status label
 */
function order_status_label(string $status): string {
    $labels = [
        'new'        => '<span class="badge bg-primary">جديد</span>',
        'processing' => '<span class="badge bg-warning text-dark">قيد التنفيذ</span>',
        'completed'  => '<span class="badge bg-success">مكتمل</span>',
        'cancelled'  => '<span class="badge bg-danger">ملغي</span>'
    ];
    return $labels[$status] ?? $status;
}

/**
 * Get current page name for navbar active state
 */
function is_active_page(string $page): string {
    $current = basename($_SERVER['PHP_SELF'], '.php');
    return $current === $page ? 'active-page' : '';
}
