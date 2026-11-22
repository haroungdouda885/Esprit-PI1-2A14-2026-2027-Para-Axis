<?php

namespace App\Models;

use App\Database;

/**
 * MEMBER 6 - table addresses (delivery addresses of a customer).
 * The Orders module can call Address::forUser($userId) at checkout, or Address::defaultFor($userId).
 */
class Address
{
    public const MAX_PER_USER = 10;

    public const GOVERNORATES = [
        'Tunis', 'Ariana', 'Ben Arous', 'Manouba', 'Nabeul', 'Zaghouan', 'Bizerte', 'Béja',
        'Jendouba', 'Le Kef', 'Siliana', 'Sousse', 'Monastir', 'Mahdia', 'Sfax', 'Kairouan',
        'Kasserine', 'Sidi Bouzid', 'Gabès', 'Medenine', 'Tataouine', 'Gafsa', 'Tozeur', 'Kebili',
    ];

    /** Default address first */
    public static function forUser(int $userId): array
    {
        return Database::query(
            'SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC',
            [$userId]
        )->fetchAll();
    }

    public static function defaultFor(int $userId): ?array
    {
        $row = Database::query('SELECT * FROM addresses WHERE user_id = ? AND is_default = 1', [$userId])->fetch();
        return $row ?: null;
    }

    /** $userId is always required: a customer can never read or change the address of someone else */
    public static function find(int $id, int $userId): ?array
    {
        $row = Database::query('SELECT * FROM addresses WHERE id = ? AND user_id = ?', [$id, $userId])->fetch();
        return $row ?: null;
    }

    public static function countForUser(int $userId): int
    {
        return (int) Database::query('SELECT COUNT(*) FROM addresses WHERE user_id = ?', [$userId])->fetchColumn();
    }

    /** $d = street, city, governorate, postal_code, phone. The first address of a customer becomes the default one. */
    public static function create(int $userId, array $d): int
    {
        $isDefault = self::countForUser($userId) === 0 ? 1 : 0;
        Database::query(
            'INSERT INTO addresses (user_id, street, city, governorate, postal_code, phone, is_default) VALUES (?, ?, ?, ?, ?, ?, ?)',
            [$userId, $d['street'], $d['city'], $d['governorate'], $d['postal_code'], $d['phone'], $isDefault]
        );
        return (int) Database::pdo()->lastInsertId();
    }

    public static function update(int $id, int $userId, array $d): void
    {
        Database::query(
            'UPDATE addresses SET street = ?, city = ?, governorate = ?, postal_code = ?, phone = ? WHERE id = ? AND user_id = ?',
            [$d['street'], $d['city'], $d['governorate'], $d['postal_code'], $d['phone'], $id, $userId]
        );
    }

    public static function setDefault(int $id, int $userId): void
    {
        if (!self::find($id, $userId)) {
            return;
        }
        $pdo = Database::pdo();
        $pdo->beginTransaction();
        Database::query('UPDATE addresses SET is_default = 0 WHERE user_id = ?', [$userId]);
        Database::query('UPDATE addresses SET is_default = 1 WHERE id = ? AND user_id = ?', [$id, $userId]);
        $pdo->commit();
    }

    public static function delete(int $id, int $userId): void
    {
        Database::query('DELETE FROM addresses WHERE id = ? AND user_id = ?', [$id, $userId]);
        // if the default address was deleted, the newest remaining one becomes the default
        if (!self::defaultFor($userId)) {
            $next = Database::query('SELECT id FROM addresses WHERE user_id = ? ORDER BY id DESC LIMIT 1', [$userId])->fetchColumn();
            if ($next) {
                self::setDefault((int) $next, $userId);
            }
        }
    }
}
