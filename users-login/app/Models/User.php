<?php

namespace App\Models;

use App\Database;

/**
 * MEMBER 1 - Users table (MySQL).
 * Every method that touches the "users" table is here, so the other members never write SQL on it.
 *
 *   used by member 2 (sign up):   create, emailTaken, verifyEmail, setVerifyToken
 *   used by member 3 (login):     check, findById
 *   used by member 4 (reset):     findByEmail, updatePassword
 *   used by member 6 (profile):   findById, updateProfile, updatePassword, emailTaken, delete
 *   used by member 1 (back office users page): search, counts, createWithRole, setBlocked, setRole, markVerified, countAdmins
 */
class User
{
    public static function findByEmail(string $email): ?array
    {
        $row = Database::query('SELECT * FROM users WHERE email = ?', [strtolower(trim($email))])->fetch();
        return $row ?: null;
    }

    public static function findById(int $id): ?array
    {
        $row = Database::query('SELECT * FROM users WHERE id = ?', [$id])->fetch();
        return $row ?: null;
    }

    /** Returns the user if email + password are correct, otherwise null */
    public static function check(string $email, string $password): ?array
    {
        $u = self::findByEmail($email);
        return ($u && password_verify($password, $u['password_hash'])) ? $u : null;
    }

    /** true if the email already belongs to another account ($exceptId = the user editing his own profile) */
    public static function emailTaken(string $email, int $exceptId = 0): bool
    {
        $u = self::findByEmail($email);
        return $u !== null && (int) $u['id'] !== $exceptId;
    }

    /**
     * Sign-up: the role is ALWAYS customer.
     * $verifyToken = the clear token sent by email (only its hash is stored in the database).
     */
    public static function create(string $name, string $email, string $password, ?string $phone = null, ?string $verifyToken = null): array
    {
        Database::query(
            'INSERT INTO users (name, email, phone, password_hash, role, is_verified, verify_token) VALUES (?, ?, ?, ?, ?, 0, ?)',
            [
                trim($name),
                strtolower(trim($email)),
                $phone,
                password_hash($password, PASSWORD_DEFAULT),
                'customer',
                $verifyToken !== null ? hash('sha256', $verifyToken) : null,
            ]
        );
        return self::findById((int) Database::pdo()->lastInsertId());
    }

    /** Confirms the email that owns this token. Returns the user id, or null if the token is wrong. */
    public static function verifyEmail(string $token): ?int
    {
        $id = Database::query('SELECT id FROM users WHERE verify_token = ?', [hash('sha256', $token)])->fetchColumn();
        if (!$id) {
            return null;
        }
        Database::query('UPDATE users SET is_verified = 1, verify_token = NULL WHERE id = ?', [$id]);
        return (int) $id;
    }

    /** Marks the account as NOT verified and stores a new token (resend email / email changed) */
    public static function setVerifyToken(int $id, string $token): void
    {
        Database::query('UPDATE users SET is_verified = 0, verify_token = ? WHERE id = ?', [hash('sha256', $token), $id]);
    }

    public static function updateProfile(int $id, string $name, string $email, ?string $phone): void
    {
        Database::query(
            'UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?',
            [trim($name), strtolower(trim($email)), $phone, $id]
        );
    }

    public static function updatePassword(int $id, string $password): void
    {
        Database::query('UPDATE users SET password_hash = ? WHERE id = ?', [password_hash($password, PASSWORD_DEFAULT), $id]);
    }

    /** Deletes the account (addresses, reset and remember-me tokens are deleted by the database: ON DELETE CASCADE) */
    public static function delete(int $id): void
    {
        Database::query('DELETE FROM users WHERE id = ?', [$id]);
    }

    // ---------- MEMBER 1 : back office "Users" page ----------

    /** Users for the admin table. Filters: $q (name / email / phone), $role (customer|admin), $status (active|blocked|unverified) */
    public static function search(string $q = '', string $role = '', string $status = ''): array
    {
        $where  = [];
        $params = [];
        if ($q !== '') {
            $like    = '%' . addcslashes($q, '%_\\') . '%';
            $where[] = '(name LIKE ? OR email LIKE ? OR phone LIKE ?)';
            array_push($params, $like, $like, $like);
        }
        if (in_array($role, ['customer', 'admin'], true)) {
            $where[]  = 'role = ?';
            $params[] = $role;
        }
        if ($status === 'blocked') {
            $where[] = 'is_blocked = 1';
        } elseif ($status === 'active') {
            $where[] = 'is_blocked = 0';
        } elseif ($status === 'unverified') {
            $where[] = 'is_verified = 0';
        }
        $sql = 'SELECT id, name, email, phone, role, is_verified, is_blocked, created_at FROM users'
             . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
             . ' ORDER BY id DESC';
        return Database::query($sql, $params)->fetchAll();
    }

    /** Numbers for the cards at the top of the page */
    public static function counts(): array
    {
        $r = Database::query(
            "SELECT COUNT(*) AS total, SUM(role = 'customer') AS customers, SUM(role = 'admin') AS admins,
                    SUM(is_blocked = 1) AS blocked, SUM(is_verified = 0) AS unverified FROM users"
        )->fetch();
        return array_map('intval', $r);
    }

    public static function countAdmins(): int
    {
        return (int) Database::query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
    }

    /** Account created by an admin (any role). The email is considered confirmed. */
    public static function createWithRole(string $name, string $email, string $password, ?string $phone, string $role): array
    {
        Database::query(
            'INSERT INTO users (name, email, phone, password_hash, role, is_verified) VALUES (?, ?, ?, ?, ?, 1)',
            [trim($name), strtolower(trim($email)), $phone, password_hash($password, PASSWORD_DEFAULT), $role]
        );
        return self::findById((int) Database::pdo()->lastInsertId());
    }

    public static function setBlocked(int $id, bool $blocked): void
    {
        Database::query('UPDATE users SET is_blocked = ? WHERE id = ?', [$blocked ? 1 : 0, $id]);
    }

    public static function setRole(int $id, string $role): void
    {
        Database::query('UPDATE users SET role = ? WHERE id = ?', [$role, $id]);
    }

    /** The admin confirms the email by hand */
    public static function markVerified(int $id): void
    {
        Database::query('UPDATE users SET is_verified = 1, verify_token = NULL WHERE id = ?', [$id]);
    }
}
