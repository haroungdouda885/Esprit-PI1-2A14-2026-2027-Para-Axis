<?php

namespace App\Models;


/**
 * Users (no database yet).
 * - The admin and demo customer accounts are written here by hand (the sign-up form can only create customers).
 * - Accounts created with the sign-up form are kept in the PHP session until the
 *   team creates the "user" table. Then only this class changes (SQL instead of arrays).
 */
class User extends Model
{
    /** Demo admin: admin@paraxis.demo / Admin123! (password stored hashed, never in clear) */
    protected static array $rows = [
        [
            'id' => 1, 'name' => 'Admin Demo', 'email' => 'admin@paraxis.demo', 'role' => 'admin',
            'password_hash' => '$2y$10$EiXvkOWm.m0GXngqkRfv0.2p5MOyLcAogp0pSdqR8MwGGhYa9LSDC',
        ],
        /** Demo customer: customer@paraxis.demo / Customer123! */
        [
            'id' => 2, 'name' => 'Demo Customer', 'email' => 'customer@paraxis.demo', 'role' => 'customer',
            'password_hash' => '$2y$10$y4BCtwqvBGDTHU9MEi4KA..faiFnfrDYHGTsX2JHHP9n0Cs/7Zyea',
        ],
    ];

    /** Fixed users + users created by sign-up (stored in the session) */
    public static function all(): array
    {
        return array_merge(static::$rows, $_SESSION['registered_users'] ?? []);
    }

    public static function findByEmail(string $email): ?array
    {
        foreach (self::all() as $u) {
            if ($u['email'] === strtolower(trim($email))) {
                return $u;
            }
        }
        return null;
    }

    /** Returns the user if email + password are correct, otherwise null */
    public static function check(string $email, string $password): ?array
    {
        $u = self::findByEmail($email);
        return ($u && password_verify($password, $u['password_hash'])) ? $u : null;
    }

    /** Sign-up: the role is ALWAYS customer */
    public static function create(string $name, string $email, string $password): array
    {
        $u = [
            'id' => count(self::all()) + 1,
            'name' => trim($name),
            'email' => strtolower(trim($email)),
            'role' => 'customer',
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ];
        $_SESSION['registered_users'][] = $u;
        return $u;
    }
}
