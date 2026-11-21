<?php

namespace App\Models;

/**
 * Entity DonationItem (module Donations & associations - member 3).
 * Donation "contient" 0..N DonationItem (donation_id) - each item "concerne" exactly 1 Product (product_id).
 */
class DonationItem extends Model
{
    protected static string $table = 'donation_items';

    protected static array $rows = [
        ['id' => 1, 'donation_id' => 2, 'product_id' => 1, 'quantity' => 2],
        ['id' => 2, 'donation_id' => 2, 'product_id' => 5, 'quantity' => 3],
    ];

    public static function forDonation(int $donationId): array
    {
        return array_values(array_filter(self::all(), fn($i) => (int) $i['donation_id'] === $donationId));
    }

    /** Items of a donation joined with their product (name, emoji, price) */
    public static function detailed(int $donationId): array
    {
        $out = [];
        foreach (self::forDonation($donationId) as $i) {
            $p = Product::find((int) $i['product_id']);
            $i['product'] = $p ?? ['name' => 'Deleted product', 'emoji' => '📦', 'price' => 0];
            $out[] = $i;
        }
        return $out;
    }

    /** true if a product is used in at least one donation (a product of a donation cannot be deleted) */
    public static function usesProduct(int $productId): bool
    {
        foreach (self::all() as $i) {
            if ((int) $i['product_id'] === $productId) {
                return true;
            }
        }
        return false;
    }
}
