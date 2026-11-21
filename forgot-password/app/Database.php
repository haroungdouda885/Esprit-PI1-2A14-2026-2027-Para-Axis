<?php

namespace App;

use PDO;
use PDOException;
use PDOStatement;

/**
 * MEMBER 1 - Database connection (PDO, one shared connection).
 * The database and the tables are created automatically at the first visit (see Schema.php):
 * only MySQL must be started, nothing to import in phpMyAdmin.
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
                // 1. connect to the MySQL server (no database yet)
                $pdo = new PDO(sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $c['host'], $c['port']), $c['user'], $c['pass'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
                // 2. create the database if needed, then use it
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $pdo->exec("USE `$name`");
                // 3. create the missing tables (+ demo accounts the first time)
                Schema::install($pdo);
                self::$pdo = $pdo;
            } catch (PDOException $e) {
                http_response_code(500);
                error_log('[Paraxis Database] ' . $e->getMessage());
                exit('Database connection failed. Is MySQL started in XAMPP? (check the db settings in app/config.php)');
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
