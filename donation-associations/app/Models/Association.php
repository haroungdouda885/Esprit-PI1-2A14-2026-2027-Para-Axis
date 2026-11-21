<?php

namespace App\Models;

/**
 * Entity Association (module Donations & associations - member 3).
 * The associations (sponsors / partners) that receive the donations: one Association receives 0..N Donation.
 * category_id -> Category of type 'association' (Health, Childhood… managed in Back office > Categories).
 */
class Association extends Model
{
    protected static string $table = 'associations';

    private const COLORS = ['#e8f1e9', '#f8efd8', '#eaf1f5', '#f1e8ed', '#e4f1f0', '#edf1df'];

    protected static array $rows = [
        ['id' => 1, 'name' => 'Sourire d\'Enfant', 'description' => 'Hygiene kits and care products for children in rural schools.', 'category_id' => 6, 'email' => 'contact@sourire-enfant.demo', 'phone' => '71 100 200', 'address' => 'Siliana', 'logo' => '', 'active' => 1, 'created_at' => '2026-10-01 09:00:00'],
        ['id' => 2, 'name' => 'Dar El Hana',        'description' => 'A home for elderly people: daily care, medicine and comfort.',    'category_id' => 8, 'email' => 'info@darelhana.demo', 'phone' => '73 220 330', 'address' => 'Sousse', 'logo' => '', 'active' => 1, 'created_at' => '2026-10-03 10:00:00'],
        ['id' => 3, 'name' => 'Main dans la Main',  'description' => 'Helps people with disabilities get the care products they need.', 'category_id' => 7, 'email' => 'hello@maindanslamain.demo', 'phone' => '74 440 550', 'address' => 'Sfax', 'logo' => '', 'active' => 1, 'created_at' => '2026-10-05 11:00:00'],
        ['id' => 4, 'name' => 'Santé Pour Tous',    'description' => 'Free health days and first-aid kits in poor neighbourhoods.',    'category_id' => 5, 'email' => 'contact@santepourtous.demo', 'phone' => '71 660 770', 'address' => 'Tunis', 'logo' => '', 'active' => 1, 'created_at' => '2026-10-08 12:00:00'],
    ];

    /** Category of the association (join category_id -> category), or null */
    public static function categoryOf(array $a): ?array
    {
        return empty($a['category_id']) ? null : Category::find((int) $a['category_id']);
    }

    public static function categoryName(array $a): string
    {
        return self::categoryOf($a)['name'] ?? 'Other';
    }

    /** Picture of the card: the emoji of its category */
    public static function emojiOf(array $a): string
    {
        return (self::categoryOf($a)['emoji'] ?? '') ?: '🤝';
    }

    public static function colorOf(array $a): string
    {
        return self::COLORS[(int) $a['id'] % count(self::COLORS)];
    }

    /** Associations of one category */
    public static function forCategory(int $categoryId): array
    {
        return array_values(array_filter(self::all(), fn($a) => (int) ($a['category_id'] ?? 0) === $categoryId));
    }

    /** Front-office list: active associations, optional category filter + text search */
    public static function search(int $categoryId = 0, string $q = ''): array
    {
        $q = mb_strtolower(trim($q));
        return array_values(array_filter(self::all(), fn($a) =>
            (int) $a['active'] === 1
            && (!$categoryId || (int) ($a['category_id'] ?? 0) === $categoryId)
            && ($q === '' || str_contains(mb_strtolower($a['name'] . ' ' . $a['description'] . ' ' . $a['address']), $q))));
    }

    /** Money received (confirmed + delivered donations, money and products) */
    public static function totalReceived(int $id): float
    {
        $sum = 0.0;
        foreach (Donation::where('association_id', $id) as $d) {
            if (in_array($d['status'], Donation::COUNTED, true)) {
                $sum += (float) $d['amount'];
            }
        }
        return round($sum, 2);
    }

    public static function donationCount(int $id): int
    {
        return count(Donation::where('association_id', $id));
    }
}
