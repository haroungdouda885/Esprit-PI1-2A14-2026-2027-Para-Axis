<?php

namespace App\Models;

/** Entity Category (module Catalog) — one Category has many Product (product.category_id) */
class Category extends Model
{
    protected static string $table = 'categories';

    protected static array $rows = [
        ['id' => 1, 'name' => 'Skincare', 'description' => 'Skincare essentials'],
        ['id' => 2, 'name' => 'Haircare', 'description' => 'Haircare essentials'],
        ['id' => 3, 'name' => 'Hygiene',  'description' => 'Hygiene essentials'],
        ['id' => 4, 'name' => 'Sun care', 'description' => 'Sun care essentials'],
    ];

    /** Deletes the category and its products (product.category_id -> category.id) */
    public static function deleteWithProducts(int $id): void
    {
        foreach (Product::forCategory($id) as $p) {
            Product::delete((int) $p['id']);
        }
        self::delete($id);
    }
}
