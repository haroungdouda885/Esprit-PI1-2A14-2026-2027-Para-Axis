<?php

namespace App\Models;

/** Entity Category — only read here (filters of the shop). Managed by the Catalog module. */
class Category extends Model
{
    protected static string $table = 'categories';

    protected static array $rows = [
        ['id' => 1, 'name' => 'Skincare', 'description' => 'Skincare essentials'],
        ['id' => 2, 'name' => 'Haircare', 'description' => 'Haircare essentials'],
        ['id' => 3, 'name' => 'Hygiene',  'description' => 'Hygiene essentials'],
        ['id' => 4, 'name' => 'Sun care', 'description' => 'Sun care essentials'],
    ];
}
