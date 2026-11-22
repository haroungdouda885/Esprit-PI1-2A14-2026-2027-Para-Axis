<?php

namespace App;

use PDO;
use PDOException;
use PDOStatement;

/**
 * MEMBER 1 - Database connection (PDO, one shared connection).
 * The database and the tables are NOT created by the site: create them yourself in phpMyAdmin (see paraxis.sql).
 * Usage in any model:  Database::query('SELECT * FROM users WHERE id = ?', [$id])->fetch();
 */
class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $c    = config('db');
            $name = preg_replace('/[^A-Za-z0-9_]/', '', (string) $c['name']); // safe database name
            try {
                // connect to the existing database (created by you in phpMyAdmin)
                $pdo = new PDO(sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $c['host'], $c['port'], $name), $c['user'], $c['pass'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
                self::$pdo = $pdo;
            } catch (PDOException $e) {
                http_response_code(500);
                error_log('[Paraxis Database] ' . $e->getMessage());
                exit('Database connection failed. Is MySQL started in XAMPP, and did you create the database `' . $name . '` and its tables in phpMyAdmin? (check the db settings in app/config.php)');
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
