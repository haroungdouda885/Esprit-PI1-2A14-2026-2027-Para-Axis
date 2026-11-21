<?php

namespace App\Models;

/**
 * Entity Donation (module Donations & associations - member 3).
 * User "fait" 0..N Donation (user_id) - Association "reçoit" 0..N Donation (association_id)
 * - one Donation "contient" 0..N DonationItem (donation_item.donation_id) when type = 'product'.
 * amount: the money given (type money) or the value of the products (type product).
 */
class Donation extends Model
{
    protected static string $table = 'donations';

    public const TYPES    = ['money' => 'Money', 'product' => 'Products'];
    public const STATUSES = ['pending', 'confirmed', 'delivered', 'cancelled'];

    /** Statuses counted in the "received" totals */
    public const COUNTED = ['confirmed', 'delivered'];

    /** The donor can cancel only a pending donation */
    public const CANCELLABLE = ['pending'];

    /** CSS class of the status pill (same pills as the orders) */
    public const PILLS = ['pending' => 'pending', 'confirmed' => 'processing', 'delivered' => 'delivered', 'cancelled' => 'shipped'];

    protected static array $rows = [
        ['id' => 1, 'user_id' => null, 'association_id' => 1, 'donor_name' => 'Sarah K.',   'donor_email' => 'sarah@example.demo',   'type' => 'money',   'amount' => 50.0, 'message' => 'Good luck with the school project!', 'anonymous' => 0, 'status' => 'confirmed', 'created_at' => '2026-11-02 10:15:00'],
        ['id' => 2, 'user_id' => null, 'association_id' => 2, 'donor_name' => 'Mohamed A.', 'donor_email' => 'mohamed@example.demo', 'type' => 'product', 'amount' => 64.5, 'message' => '',                                  'anonymous' => 1, 'status' => 'delivered', 'created_at' => '2026-11-04 16:30:00'],
        ['id' => 3, 'user_id' => null, 'association_id' => 4, 'donor_name' => 'Lina N.',    'donor_email' => 'lina@example.demo',    'type' => 'money',   'amount' => 20.0, 'message' => 'For the first-aid kits.',            'anonymous' => 0, 'status' => 'pending',   'created_at' => '2026-11-09 09:40:00'],
    ];

    public static function pill(string $status): string
    {
        return self::PILLS[$status] ?? 'pending';
    }

    /** Donations of one user, newest first */
    public static function forUser(int $userId): array
    {
        return array_reverse(array_values(array_filter(self::all(), fn($d) => (int) ($d['user_id'] ?? 0) === $userId)));
    }

    /** Back-office list: association / type / status filters + search, newest first */
    public static function filter(int $associationId = 0, string $type = '', string $status = '', string $q = ''): array
    {
        $q = mb_strtolower(trim($q));
        return array_reverse(array_values(array_filter(self::all(), fn($d) =>
            (!$associationId || (int) $d['association_id'] === $associationId)
            && ($type === '' || $d['type'] === $type)
            && ($status === '' || $d['status'] === $status)
            && ($q === '' || str_contains(mb_strtolower($d['donor_name'] . ' ' . $d['donor_email'] . ' ' . $d['message']), $q)))));
    }

    /** Name shown in public lists: "Anonymous" when the donor asked for it */
    public static function publicName(array $d): string
    {
        return (int) $d['anonymous'] === 1 ? 'Anonymous' : $d['donor_name'];
    }

    /**
     * Product donations: the products are taken from the shop stock when the donation is created (-1)
     * and put back when it is cancelled or deleted before delivery (+1).
     */
    public static function moveStock(int $donationId, int $direction): void
    {
        foreach (DonationItem::forDonation($donationId) as $item) {
            Product::changeStock((int) $item['product_id'], $direction * (int) $item['quantity']);
        }
    }

    /** Deletes the donation and its items (ON DELETE CASCADE) */
    public static function deleteWithItems(int $id): void
    {
        foreach (DonationItem::forDonation($id) as $item) {
            DonationItem::delete((int) $item['id']);
        }
        self::delete($id);
    }
}
