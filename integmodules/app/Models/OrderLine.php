<?php

namespace App\Models;

/** Entity OrderLine — linked to Order by order_id (product_name and unit_price are a snapshot) */
class OrderLine extends Model
{
    protected static string $table = 'order_lines';

    protected static array $rows = [];
}
