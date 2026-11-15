<?php

namespace App\Models;

/**
 * Entity Product — managed by the Catalog module.
 * Used here to list what can be put in the bag and to update the stock
 * (stock goes down when an order is placed, back up when it is cancelled).
 */
class Product extends Model
{
    protected static string $table = 'products';

    protected static array $rows = [
        ['id' => 1, 'category_id' => 1, 'name' => 'Daily Dew Moisturizer', 'brand' => 'ParaSanté', 'description' => 'A skincare pick for your routine.', 'price' => 18.9, 'stock' => 32, 'emoji' => '🧴', 'color' => '#e8f1e9'],
        ['id' => 2, 'category_id' => 1, 'name' => 'Soft Cloud Cleanser',   'brand' => 'ParaSanté', 'description' => 'A skincare pick for your routine.', 'price' => 14.5, 'stock' => 5,  'emoji' => '🫧', 'color' => '#eaf1f5'],
        ['id' => 3, 'category_id' => 4, 'name' => 'Sun Day SPF Cream',     'brand' => 'ParaSanté', 'description' => 'A sun care pick for your routine.', 'price' => 29.9, 'stock' => 12, 'emoji' => '☀️', 'color' => '#f8efd8'],
        ['id' => 4, 'category_id' => 2, 'name' => 'Botanical Shampoo',     'brand' => 'ParaSanté', 'description' => 'A haircare pick for your routine.', 'price' => 22.0, 'stock' => 18, 'emoji' => '🧴', 'color' => '#e6eee0'],
        ['id' => 5, 'category_id' => 3, 'name' => 'Fresh Hand Wash',       'brand' => 'ParaSanté', 'description' => 'A hygiene pick for your routine.',  'price' => 8.9,  'stock' => 3,  'emoji' => '🧼', 'color' => '#e4f1f0'],
        ['id' => 6, 'category_id' => 2, 'name' => 'Silky Conditioner',     'brand' => 'ParaSanté', 'description' => 'A haircare pick for your routine.', 'price' => 19.5, 'stock' => 26, 'emoji' => '💆', 'color' => '#f1e8ed'],
        ['id' => 7, 'category_id' => 3, 'name' => 'Daily Oral Care Set',   'brand' => 'ParaSanté', 'description' => 'A hygiene pick for your routine.',  'price' => 12.0, 'stock' => 11, 'emoji' => '🪥', 'color' => '#e8eef7'],
        ['id' => 8, 'category_id' => 4, 'name' => 'After Sun Lotion',      'brand' => 'ParaSanté', 'description' => 'A sun care pick for your routine.', 'price' => 24.0, 'stock' => 14, 'emoji' => '🌿', 'color' => '#edf1df'],
    ];

    /** Products of a category (or all) whose name / brand / category matches $search */
    public static function search(?int $categoryId, string $search): array
    {
        $search = mb_strtolower(trim($search));
        return array_values(array_filter(self::all(), function ($p) use ($categoryId, $search) {
            if ($categoryId && (int) $p['category_id'] !== $categoryId) {
                return false;
            }
            $category = Category::find((int) $p['category_id'])['name'] ?? '';
            return $search === '' || str_contains(mb_strtolower($p['name'] . ' ' . $category . ' ' . $p['brand']), $search);
        }));
    }

    /** stock = stock + $delta (never below 0) */
    public static function changeStock(int $id, int $delta): void
    {
        $p = self::find($id);
        if ($p) {
            self::update($id, ['stock' => max(0, (int) $p['stock'] + $delta)]);
        }
    }
}
