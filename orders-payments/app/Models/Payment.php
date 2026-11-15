<?php

namespace App\Models;

/** Entity Payment — linked to Order by order_id (one payment per order) */
class Payment extends Model
{
    protected static string $table = 'payments';

    public const STATUSES = ['pending', 'paid', 'failed', 'refunded'];

    public const METHODS = [
        'card'             => 'Card (simulated payment)',
        'cash_on_delivery' => 'Cash on delivery',
        'bank_transfer'    => 'Bank transfer',
    ];

    protected static array $rows = [
        ['id' => 1, 'order_id' => 1, 'amount' => 68.4, 'method' => 'card', 'status' => 'paid', 'paid_at' => '2026-11-03 10:20', 'transaction_reference' => null],
        ['id' => 2, 'order_id' => 2, 'amount' => 42.9, 'method' => 'card', 'status' => 'paid', 'paid_at' => '2026-11-05 15:40', 'transaction_reference' => null],
        ['id' => 3, 'order_id' => 3, 'amount' => 91.5, 'method' => 'card', 'status' => 'paid', 'paid_at' => '2026-11-08 09:05', 'transaction_reference' => null],
    ];

    /** Payment of an order (join payment.order_id -> order.id) */
    public static function forOrder(int $orderId): ?array
    {
        return self::where('order_id', $orderId)[0] ?? null;
    }
}
