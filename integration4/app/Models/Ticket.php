<?php

namespace App\Models;

/**
 * Entity Ticket (module Customer support — member 1).
 * Uses the shared Model base (session storage until the team database exists).
 * ticket_reply.ticket_id -> ticket.id
 */
class Ticket extends Model
{
    protected static string $table = 'tickets';

    protected static array $rows = [];

    /** Tickets of one customer (front office) */
    public static function forCustomer(string $email): array
    {
        return self::where('customer_email', $email);
    }

    /**
     * Opens a ticket for the logged-in user.
     * New link of the class diagram: Ticket "concerne" 0..1 Product (product_id nullable).
     */
    public static function open(array $user, string $subject, string $message, ?int $productId = null): array
    {
        return self::insert([
            'user_id'        => isset($user['id']) ? (int) $user['id'] : null,
            'product_id'     => $productId,
            'customer_name'  => $user['name'] ?? 'Customer',
            'customer_email' => $user['email'] ?? '',
            'subject'        => $subject,
            'message'        => $message,
            'status'         => 'open',
            'created_at'     => date('Y-m-d H:i:s'),
        ]);
    }

    /** The product the ticket is about, or null (join product_id -> product) */
    public static function productOf(array $ticket): ?array
    {
        return empty($ticket['product_id']) ? null : Product::find((int) $ticket['product_id']);
    }

    /** ON DELETE SET NULL: a deleted product is removed from its tickets */
    public static function detachProduct(int $productId): void
    {
        foreach (self::all() as $t) {
            if ((int) ($t['product_id'] ?? 0) === $productId) {
                self::update((int) $t['id'], ['product_id' => null]);
            }
        }
    }

    public static function updateStatus(int $id, string $status): void
    {
        self::update($id, ['status' => $status]);
    }

    /** Deletes the ticket and its whole conversation */
    public static function deleteWithReplies(int $id): void
    {
        foreach (TicketReply::forTicket($id) as $r) {
            TicketReply::delete((int) $r['id']);
        }
        self::delete($id);
    }
}
