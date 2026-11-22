<?php

namespace App\Models;

use App\Database;

/** MEMBER 4 - table password_resets */
class PasswordReset
{
    /** Creates a token valid for $minutes. Older tokens of the same user are removed (only the last email works). */
    public static function create(int $userId, string $tokenHash, int $minutes = 60): void
    {
        $minutes = max(1, $minutes);
        Database::query('DELETE FROM password_resets WHERE user_id = ?', [$userId]);
        Database::query(
            "INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL {$minutes} MINUTE))",
            [$userId, $tokenHash]
        );
    }

    /** The row if the token exists, is not expired and was not used yet */
    public static function findValid(string $tokenHash): ?array
    {
        $row = Database::query(
            'SELECT * FROM password_resets WHERE token_hash = ? AND used = 0 AND expires_at > NOW()',
            [$tokenHash]
        )->fetch();
        return $row ?: null;
    }

    public static function markUsed(int $id): void
    {
        Database::query('UPDATE password_resets SET used = 1 WHERE id = ?', [$id]);
    }
}
