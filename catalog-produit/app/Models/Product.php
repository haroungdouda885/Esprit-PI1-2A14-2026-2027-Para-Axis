<?php

namespace App\Models;

/** Entity Product — linked to Category by category_id */
class Product extends Model
{
    protected static string $table = 'products';

    /** Stock at or below this number is shown as "Low stock" */
    public const LOW_STOCK = 5;

    protected static array $rows = [
        ['id' => 1, 'category_id' => 1, 'name' => 'Daily Dew Moisturizer', 'brand' => 'ParaSanté', 'price' => 18.9, 'stock' => 32, 'emoji' => '🧴', 'color' => '#e8f1e9', 'description' => 'A light daily cream that keeps your skin soft and hydrated.'],
        ['id' => 2, 'category_id' => 1, 'name' => 'Soft Cloud Cleanser',   'brand' => 'ParaSanté', 'price' => 14.5, 'stock' => 5,  'emoji' => '🫧', 'color' => '#eaf1f5', 'description' => 'A gentle foaming cleanser for every skin type.'],
        ['id' => 3, 'category_id' => 4, 'name' => 'Sun Day SPF Cream',     'brand' => 'ParaSanté', 'price' => 29.9, 'stock' => 12, 'emoji' => '☀️', 'color' => '#f8efd8', 'description' => 'SPF 50 protection for the face, non-greasy finish.'],
        ['id' => 4, 'category_id' => 2, 'name' => 'Botanical Shampoo',     'brand' => 'ParaSanté', 'price' => 22.0, 'stock' => 18, 'emoji' => '🧴', 'color' => '#e6eee0', 'description' => 'A mild shampoo with plant extracts for daily use.'],
        ['id' => 5, 'category_id' => 3, 'name' => 'Fresh Hand Wash',       'brand' => 'ParaSanté', 'price' => 8.9,  'stock' => 3,  'emoji' => '🧼', 'color' => '#e4f1f0', 'description' => 'Cleans gently and leaves your hands fresh.'],
        ['id' => 6, 'category_id' => 2, 'name' => 'Silky Conditioner',     'brand' => 'ParaSanté', 'price' => 19.5, 'stock' => 26, 'emoji' => '💆', 'color' => '#f1e8ed', 'description' => 'Detangles and softens without weighing hair down.'],
        ['id' => 7, 'category_id' => 3, 'name' => 'Daily Oral Care Set',   'brand' => 'ParaSanté', 'price' => 12.0, 'stock' => 11, 'emoji' => '🪥', 'color' => '#e8eef7', 'description' => 'Toothbrush and toothpaste for a complete routine.'],
        ['id' => 8, 'category_id' => 4, 'name' => 'After Sun Lotion',      'brand' => 'ParaSanté', 'price' => 24.0, 'stock' => 0,  'emoji' => '🌿', 'color' => '#edf1df', 'description' => 'Soothes and cools the skin after sun exposure.'],
    ];

    /** Products of one category */
    public static function forCategory(int $categoryId): array
    {
        return array_values(array_filter(self::all(), fn($p) => (int) $p['category_id'] === $categoryId));
    }

    /** Front-office list: optional category filter + text search (name, brand, description) */
    public static function search(int $categoryId = 0, string $q = ''): array
    {
        $q = mb_strtolower(trim($q));
        return array_values(array_filter(self::all(), function ($p) use ($categoryId, $q) {
            if ($categoryId && (int) $p['category_id'] !== $categoryId) {
                return false;
            }
            return $q === '' || str_contains(mb_strtolower($p['name'] . ' ' . $p['brand'] . ' ' . $p['description']), $q);
        }));
    }

    /** 'In stock' / 'Low stock' / 'Out of stock' + the CSS class of the status pill (back office) */
    public static function stockStatus(int $stock): array
    {
        if ($stock <= 0) {
            return ['Out of stock', 'pending'];
        }
        return $stock <= self::LOW_STOCK ? ['Low stock', 'processing'] : ['In stock', 'delivered'];
    }
}
