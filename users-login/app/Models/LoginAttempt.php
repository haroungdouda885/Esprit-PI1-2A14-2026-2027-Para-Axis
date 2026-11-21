<?php

namespace App\Models;

use App\Database;

/** MEMBER 5 - table login_attempts (failed logins) */
class LoginAttempt
{
    public static function record(string $email, string $ip): void
    {
        Database::query('INSERT INTO login_attempts (email, ip_address) VALUES (?, ?)', [$email, $ip]);
    }

    public static function countByEmail(string $email, int $minutes): int
    {
        $minutes = max(1, $minutes);
        return (int) Database::query(
            "SELECT COUNT(*) FROM login_attempts WHERE email = ? AND attempted_at > (NOW() - INTERVAL {$minutes} MINUTE)",
            [$email]
        )->fetchColumn();
    }

    public static function countByIp(string $ip, int $minutes): int
    {
        $minutes = max(1, $minutes);
        return (int) Database::query(
            "SELECT COUNT(*) FROM login_attempts WHERE ip_address = ? AND attempted_at > (NOW() - INTERVAL {$minutes} MINUTE)",
            [$ip]
        )->fetchColumn();
    }

    public static function clearForEmail(string $email): void
    {
        Database::query('DELETE FROM login_attempts WHERE email = ?', [$email]);
    }
}
