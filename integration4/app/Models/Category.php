<?php

namespace App\Models;

/**
 * Entity Category — shared by two modules, the "type" column says which one:
 *   type 'product'     : Catalog (member 2) — one Category groups 0..N Product (product.category_id)
 *   type 'association' : Donations (member 3) — one Category groups 0..N Association (association.category_id)
 * Also read by the Orders module (shop filters).
 */
class Category extends Model
{
    protected static string $table = 'categories';

    public const TYPES = ['product' => 'Products', 'association' => 'Associations'];

    protected static array $rows = [
        ['id' => 1, 'type' => 'product',     'name' => 'Skincare',       'emoji' => '🧴', 'description' => 'Skincare essentials'],
        ['id' => 2, 'type' => 'product',     'name' => 'Haircare',       'emoji' => '💆', 'description' => 'Haircare essentials'],
        ['id' => 3, 'type' => 'product',     'name' => 'Hygiene',        'emoji' => '🧼', 'description' => 'Hygiene essentials'],
        ['id' => 4, 'type' => 'product',     'name' => 'Sun care',       'emoji' => '☀️', 'description' => 'Sun care essentials'],
        ['id' => 5, 'type' => 'association', 'name' => 'Health',         'emoji' => '🩺', 'description' => 'Care, medicine and first aid'],
        ['id' => 6, 'type' => 'association', 'name' => 'Childhood',      'emoji' => '🧸', 'description' => 'Children and schools'],
        ['id' => 7, 'type' => 'association', 'name' => 'Disability',     'emoji' => '♿', 'description' => 'People with disabilities'],
        ['id' => 8, 'type' => 'association', 'name' => 'Elderly people', 'emoji' => '👵', 'description' => 'Homes and care for elderly people'],
        ['id' => 9, 'type' => 'association', 'name' => 'Other',          'emoji' => '🤝', 'description' => 'Other causes'],
    ];

    /** Type of a row ('product' for old rows saved before the column existed) */
    public static function typeOf(array $c): string
    {
        return ($c['type'] ?? 'product') === 'association' ? 'association' : 'product';
    }

    /** Categories of one type: 'product' or 'association' */
    public static function ofType(string $type): array
    {
        return array_values(array_filter(self::all(), fn($c) => self::typeOf($c) === $type));
    }

    /** The category if it exists AND has this type (used to check the forms) */
    public static function findOfType(int $id, string $type): ?array
    {
        $c = self::find($id);
        return $c && self::typeOf($c) === $type ? $c : null;
    }

    /** Number of products or associations in the category */
    public static function itemCount(array $c): int
    {
        return self::typeOf($c) === 'association'
            ? count(Association::forCategory((int) $c['id']))
            : count(Product::forCategory((int) $c['id']));
    }

    /** Deletes the category and its products (product.category_id -> category.id) */
    public static function deleteWithProducts(int $id): void
    {
        foreach (Product::forCategory($id) as $p) {
            Ticket::detachProduct((int) $p['id']); // tickets and quizzes stay, without product
            Quiz::detachProduct((int) $p['id']);
            Product::delete((int) $p['id']);
        }
        self::delete($id);
    }
}
