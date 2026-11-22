<?php

namespace App;

use PDO;
use PDOException;
use PDOStatement;

/**
 * MEMBER 1 - Database connection (PDO, one shared connection).
 * Usage in any model:  Database::query('SELECT * FROM users WHERE id = ?', [$id])->fetch();
 */
class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $c   = config('db');
            $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $c['host'], $c['port'], $c['name']);
            try {
                self::$pdo = new PDO($dsn, $c['user'], $c['pass'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                http_response_code(500);
                exit("Database connection failed. Is MySQL started in XAMPP, and did you import the files of the 'database' folder? (check app/config.php)");
            }
        }
        return self::$pdo;
    }

    /** Runs a prepared statement and returns it (use ->fetch(), ->fetchAll(), ->fetchColumn(), ->rowCount()) */
    public static function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
