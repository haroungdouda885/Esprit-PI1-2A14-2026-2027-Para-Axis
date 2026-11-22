<?php

namespace App;

use App\Models\AuthToken;
use App\Models\User;

/**
 * MEMBER 3 - "Remember me" (cookie with a selector + a secret; only the hash of the secret is in the database).
 * Auth::restore() is called once per request in index.php.
 */
class Auth
{
    private const COOKIE = 'paraxis_remember';
    private const DAYS   = 30;

    /** Creates the cookie + database row for this user */
    public static function remember(int $userId): void
    {
        $selector  = bin2hex(random_bytes(9));   // 18 characters
        $validator = bin2hex(random_bytes(32));
        AuthToken::create($userId, $selector, hash('sha256', $validator), self::DAYS);
        self::setCookie($selector . ':' . $validator, time() + self::DAYS * 86400);
    }

    /** Logs the user in again from the cookie (if valid). The token is replaced each time it is used. */
    public static function restore(): void
    {
        if (currentUser() || empty($_COOKIE[self::COOKIE]) || !is_string($_COOKIE[self::COOKIE])) {
            return;
        }
        [$selector, $validator] = array_pad(explode(':', $_COOKIE[self::COOKIE], 2), 2, '');
        $row = AuthToken::findValid($selector);

        if ($row && hash_equals($row['token_hash'], hash('sha256', $validator))) {
            $user = User::findById((int) $row['user_id']);
            if ($user && !$user['is_blocked']) {
                AuthToken::delete($selector);
                loginUser($user);
                self::remember((int) $user['id']);
                return;
            }
        }
        self::forget(); // invalid / expired / stolen cookie: remove it
    }

    /** Removes the cookie and its database row (logout) */
    public static function forget(): void
    {
        if (!empty($_COOKIE[self::COOKIE]) && is_string($_COOKIE[self::COOKIE])) {
            AuthToken::delete(explode(':', $_COOKIE[self::COOKIE], 2)[0]);
        }
        self::setCookie('', time() - 3600);
        unset($_COOKIE[self::COOKIE]);
    }

    private static function setCookie(string $value, int $expires): void
    {
        setcookie(self::COOKIE, $value, [
            'expires'  => $expires,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}
