<?php
/**
 * علامة | ALAMAH — Database Connection (PDO Singleton)
 */

require_once __DIR__ . '/../config/database.php';

class Database {
    private static ?PDO $instance = null;

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            try {
                $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE  => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES    => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND  => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ]);
            } catch (PDOException $e) {
                die('خطأ في الاتصال بقاعدة البيانات: ' . $e->getMessage());
            }
        }
        return self::$instance;
    }

    // Prevent cloning and unserialization
    private function __construct() {}
    private function __clone() {}
    public function __wakeup() { throw new \Exception("Cannot unserialize singleton"); }
}
