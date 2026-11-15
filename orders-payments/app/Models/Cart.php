<?php

namespace App\Models;

/**
 * Shopping bag of the visitor (not a table): $_SESSION['cart'] = [product_id => quantity].
 * Prices and names are always read again from Product, so the bag is never out of date.
 */
class Cart
{
    public static function items(): array
    {
        // remove products that no longer exist
        $items = array_filter($_SESSION['cart'] ?? [], fn($qty, $id) => $qty > 0 && Product::find((int) $id), ARRAY_FILTER_USE_BOTH);
        $_SESSION['cart'] = $items;
        return $items;
    }

    /** Lines ready to display / to order: product + qty + line_total */
    public static function lines(): array
    {
        $lines = [];
        foreach (self::items() as $id => $qty) {
            $p = Product::find((int) $id);
            $lines[] = ['product' => $p, 'qty' => $qty, 'line_total' => round($p['price'] * $qty, 3)];
        }
        return $lines;
    }

    public static function quantity(int $productId): int
    {
        return self::items()[$productId] ?? 0;
    }

    public static function count(): int
    {
        return array_sum(self::items());
    }

    public static function total(): float
    {
        return round(array_sum(array_column(self::lines(), 'line_total')), 3);
    }

    /** Sets the quantity (0 removes the product) */
    public static function set(int $productId, int $qty): void
    {
        self::items();
        if ($qty <= 0) {
            unset($_SESSION['cart'][$productId]);
        } else {
            $_SESSION['cart'][$productId] = $qty;
        }
    }

    public static function clear(): void
    {
        $_SESSION['cart'] = [];
    }

    /** First visit of the shop / bag: a ready-to-test sample bag (it can be emptied or edited) */
    public static function initDemoOnce(): void
    {
        if (!isset($_SESSION['demo_bag_initialized'])) {
            $_SESSION['demo_bag_initialized'] = true;
            self::loadDemo();
        }
    }

    /** Sample bag: 2 units of the first product in stock, 1 unit of the next two */
    public static function loadDemo(): void
    {
        $inStock = array_values(array_filter(Product::all(), fn($p) => $p['stock'] > 0));
        $_SESSION['cart'] = [];
        foreach (array_slice($inStock, 0, 3) as $i => $p) {
            $_SESSION['cart'][(int) $p['id']] = $i === 0 ? 2 : 1;
        }
    }
}
