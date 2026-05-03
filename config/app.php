<?php
/**
 * علامة | ALAMAH — Application Configuration
 */

// Site
define('SITE_NAME', 'علامة | ALAMAH');
define('SITE_URL', 'http://localhost/Alamah%20v3');
define('BASE_PATH', dirname(__DIR__));

// Brevo (Sendinblue) API
define('BREVO_API_KEY', getenv('BREVO_API_KEY') ?: '');
define('BREVO_SENDER_EMAIL', 'noreply@alamah.sa');
define('BREVO_SENDER_NAME', 'علامة ALAMAH');

// Verification
define('VERIFICATION_CODE_LENGTH', 6);
define('VERIFICATION_CODE_EXPIRY', 15); // minutes

// Upload
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
define('UPLOAD_PRODUCTS_DIR', BASE_PATH . '/uploads/products/');
define('UPLOAD_SLIDES_DIR', BASE_PATH . '/uploads/slides/');

// WhatsApp
define('WA_NUMBER', '967784449090');
