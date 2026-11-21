<?php

namespace App\Models;

use App\Database;

/** MEMBER 3 - table auth_tokens ("remember me" cookies) */
class AuthToken
{
    public static function create(int $userId, string $selector, string $tokenHash, int $days): void
    {
        $days = max(1, $days);
        Database::query(
            "INSERT INTO auth_tokens (user_id, selector, token_hash, expires_at) VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL {$days} DAY))",
            [$userId, $selector, $tokenHash]
        );
    }

    public static function findValid(string $selector): ?array
    {
        $row = Database::query('SELECT * FROM auth_tokens WHERE selector = ? AND expires_at > NOW()', [$selector])->fetch();
        return $row ?: null;
    }

    public static function delete(string $selector): void
    {
        Database::query('DELETE FROM auth_tokens WHERE selector = ?', [$selector]);
    }

    /** Called when the password changes: every "remembered" device must log in again */
    public static function deleteForUser(int $userId): void
    {
        Database::query('DELETE FROM auth_tokens WHERE user_id = ?', [$userId]);
    }
}
