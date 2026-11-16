<?php

namespace App\Models;

/**
 * Entity Order (module Orders and payments).
 * Children: OrderLine (order_line.order_id -> order.id) and Payment (payment.order_id -> order.id).
 * 'items' keeps a snapshot of the bag (name, price, qty) for the tables of the front and back office.
 */
class Order extends Model
{
    protected static string $table = 'orders';

    public const STATUSES = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

    /** The customer can cancel only these statuses */
    public const CANCELLABLE = ['pending', 'processing'];

    protected static array $rows = [
        ['id' => 1, 'user_id' => null, 'customer_name' => 'Sarah K.',   'customer_email' => 'sarah@example.demo',   'shipping_address' => 'Tunis', 'items' => [], 'total' => 68.4, 'status' => 'pending',   'created_at' => '2026-11-03 10:20'],
        ['id' => 2, 'user_id' => null, 'customer_name' => 'Mohamed A.', 'customer_email' => 'mohamed@example.demo', 'shipping_address' => 'Tunis', 'items' => [], 'total' => 42.9, 'status' => 'shipped',   'created_at' => '2026-11-05 15:40'],
        ['id' => 3, 'user_id' => null, 'customer_name' => 'Lina N.',    'customer_email' => 'lina@example.demo',    'shipping_address' => 'Tunis', 'items' => [], 'total' => 91.5, 'status' => 'delivered', 'created_at' => '2026-11-08 09:05'],
    ];

    /** Orders of one customer, newest first */
    public static function forUser(int $userId): array
    {
        return array_reverse(array_values(array_filter(self::all(), fn($o) => (int) ($o['user_id'] ?? 0) === $userId)));
    }

    public static function isCancellable(array $order): bool
    {
        return in_array($order['status'], self::CANCELLABLE, true);
    }

    /** Puts the purchased quantities back in stock (+1) or takes them again (-1) */
    public static function moveStock(array $order, int $direction): void
    {
        foreach ($order['items'] ?? [] as $item) {
            Product::changeStock((int) $item['product_id'], $direction * (int) $item['qty']);
        }
    }

    /** Deletes the order, its payment and its lines (ON DELETE CASCADE) */
    public static function deleteWithChildren(int $id): void
    {
        foreach (Payment::where('order_id', $id) as $p) {
            Payment::delete((int) $p['id']);
        }
        foreach (OrderLine::where('order_id', $id) as $l) {
            OrderLine::delete((int) $l['id']);
        }
        self::delete($id);
    }
}
