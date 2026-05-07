<?php
/**
 * علامة | ALAMAH — Mailer (Brevo SMTP API v3)
 */

require_once __DIR__ . '/../config/app.php';

class Mailer {
    private static string $lastError = '';

    public static function getLastError(): string {
        return self::$lastError;
    }

    /**
     * Send email via Brevo API
     */
    public static function send(string $toEmail, string $toName, string $subject, string $htmlContent): bool {
        self::$lastError = '';

        if (BREVO_API_KEY === '') {
            self::$lastError = 'BREVO_API_KEY is not configured';
            error_log('Brevo API Error: ' . self::$lastError);
            return false;
        }

        if (!function_exists('curl_init')) {
            self::$lastError = 'PHP cURL extension is not enabled';
            error_log('Brevo API Error: ' . self::$lastError);
            return false;
        }

        $url = 'https://api.brevo.com/v3/smtp/email';

        $data = [
            'sender'  => ['name' => BREVO_SENDER_NAME, 'email' => BREVO_SENDER_EMAIL],
            'to'      => [['email' => $toEmail, 'name' => $toName]],
            'subject' => $subject,
            'htmlContent' => $htmlContent
        ];

        $payload = json_encode($data, JSON_UNESCAPED_UNICODE);
        if ($payload === false) {
            self::$lastError = 'Failed to encode Brevo email payload: ' . json_last_error_msg();
            error_log('Brevo API Error: ' . self::$lastError);
            return false;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'accept: application/json',
                'api-key: ' . BREVO_API_KEY,
                'content-type: application/json'
            ],
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // Log errors for debugging
        if ($httpCode < 200 || $httpCode >= 300) {
            self::$lastError = trim("HTTP {$httpCode}: {$response} {$curlError}");
            error_log("Brevo API Error [{$httpCode}]: {$response} | cURL: {$curlError}");
        }

        return $httpCode >= 200 && $httpCode < 300;
    }

    /**
     * Send verification code email
     */
    public static function sendVerificationCode(string $email, string $name, string $code): bool {
        $subject = 'رمز التحقق — علامة ALAMAH';
        $html = self::buildVerificationEmail($name, $code);
        return self::send($email, $name, $subject, $html);
    }

    /**
     * Send password reset code
     */
    public static function sendPasswordResetCode(string $email, string $name, string $code): bool {
        $subject = 'إعادة تعيين كلمة المرور — علامة ALAMAH';
        $html = self::buildPasswordResetEmail($name, $code);
        return self::send($email, $name, $subject, $html);
    }

    /**
     * Build verification email HTML
     */
    private static function buildVerificationEmail(string $name, string $code): string {
        return '
        <!DOCTYPE html>
        <html dir="rtl" lang="ar">
        <head><meta charset="UTF-8"></head>
        <body style="margin:0;padding:0;background:#F7F1E8;font-family:Tahoma,Arial,sans-serif;">
        <div style="max-width:500px;margin:40px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
            <div style="background:linear-gradient(135deg,#1B2A5B,#2A3F7E);padding:30px;text-align:center;">
                <h1 style="color:#C9A96E;margin:0;font-size:28px;">علامة ALAMAH</h1>
                <p style="color:rgba(255,255,255,0.8);margin:8px 0 0;font-size:14px;">تحقق من بريدك الإلكتروني</p>
            </div>
            <div style="padding:30px;text-align:center;">
                <p style="color:#1B2A5B;font-size:16px;margin-bottom:8px;">مرحباً <strong>' . htmlspecialchars($name) . '</strong></p>
                <p style="color:#8A8580;font-size:14px;margin-bottom:24px;">استخدم الرمز التالي لتأكيد حسابك:</p>
                <div style="background:#F7F1E8;border-radius:12px;padding:20px;display:inline-block;margin-bottom:24px;">
                    <span style="font-size:36px;letter-spacing:12px;color:#1B2A5B;font-weight:bold;">' . $code . '</span>
                </div>
                <p style="color:#8A8580;font-size:13px;">هذا الرمز صالح لمدة <strong>' . VERIFICATION_CODE_EXPIRY . ' دقيقة</strong></p>
                <p style="color:#D63B2F;font-size:12px;margin-top:16px;">إذا لم تطلب هذا الرمز، يرجى تجاهل هذا البريد.</p>
            </div>
            <div style="background:#FAFAFA;padding:16px;text-align:center;border-top:1px solid #eee;">
                <p style="color:#B5AFA8;font-size:12px;margin:0;">© ' . date('Y') . ' علامة ALAMAH — جميع الحقوق محفوظة</p>
            </div>
        </div>
        </body>
        </html>';
    }

    /**
     * Build password reset email HTML
     */
    private static function buildPasswordResetEmail(string $name, string $code): string {
        return '
        <!DOCTYPE html>
        <html dir="rtl" lang="ar">
        <head><meta charset="UTF-8"></head>
        <body style="margin:0;padding:0;background:#F7F1E8;font-family:Tahoma,Arial,sans-serif;">
        <div style="max-width:500px;margin:40px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
            <div style="background:linear-gradient(135deg,#D63B2F,#E05A4F);padding:30px;text-align:center;">
                <h1 style="color:#fff;margin:0;font-size:28px;">علامة ALAMAH</h1>
                <p style="color:rgba(255,255,255,0.8);margin:8px 0 0;font-size:14px;">إعادة تعيين كلمة المرور</p>
            </div>
            <div style="padding:30px;text-align:center;">
                <p style="color:#1B2A5B;font-size:16px;margin-bottom:8px;">مرحباً <strong>' . htmlspecialchars($name) . '</strong></p>
                <p style="color:#8A8580;font-size:14px;margin-bottom:24px;">استخدم الرمز التالي لإعادة تعيين كلمة المرور:</p>
                <div style="background:#F7F1E8;border-radius:12px;padding:20px;display:inline-block;margin-bottom:24px;">
                    <span style="font-size:36px;letter-spacing:12px;color:#D63B2F;font-weight:bold;">' . $code . '</span>
                </div>
                <p style="color:#8A8580;font-size:13px;">هذا الرمز صالح لمدة <strong>' . VERIFICATION_CODE_EXPIRY . ' دقيقة</strong></p>
            </div>
            <div style="background:#FAFAFA;padding:16px;text-align:center;border-top:1px solid #eee;">
                <p style="color:#B5AFA8;font-size:12px;margin:0;">© ' . date('Y') . ' علامة ALAMAH — جميع الحقوق محفوظة</p>
            </div>
        </div>
        </body>
        </html>';
    }
}
