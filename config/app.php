<?php
/**
 * علامة | ALAMAH — Application Configuration
 */

// Site
define('SITE_NAME', 'علامة | ALAMAH');
define('BASE_PATH', dirname(__DIR__));

$secretsFile = __DIR__ . '/secrets.php';
$secrets = is_file($secretsFile) ? require $secretsFile : [];

define('SITE_URL', getenv('SITE_URL') ?: ($secrets['SITE_URL'] ?? 'https://3lamah.com'));

// Brevo (Sendinblue) API
define('BREVO_API_KEY', trim(getenv('BREVO_API_KEY') ?: ($secrets['BREVO_API_KEY'] ?? '')));
define('BREVO_SENDER_EMAIL', getenv('BREVO_SENDER_EMAIL') ?: ($secrets['BREVO_SENDER_EMAIL'] ?? 'info@3lamah.com'));
define('BREVO_SENDER_NAME', 'علامة ALAMAH');

// Verification
define('VERIFICATION_CODE_LENGTH', 6);
define('VERIFICATION_CODE_EXPIRY', 15); // minutes
define('VERIFICATION_RESEND_COOLDOWN', 4); // minutes

// Upload
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
define('UPLOAD_PRODUCTS_DIR', BASE_PATH . '/uploads/products/');
define('UPLOAD_SLIDES_DIR', BASE_PATH . '/uploads/slides/');

// WhatsApp
define('WA_NUMBER', '967784449090');
