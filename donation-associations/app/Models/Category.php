<?php

namespace App\Models;

/**
 * Entity Category — shared with the Catalog module (member 2); the "type" column says which module uses it:
 *   type 'product'     : Catalog — Category "regroupe" Product (managed by member 2, read-only here)
 *   type 'association' : Donations (my module) — Category "regroupe" Association (association.category_id)
 * In the integrated project both types are managed on the same back-office "Categories" page.
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
}
