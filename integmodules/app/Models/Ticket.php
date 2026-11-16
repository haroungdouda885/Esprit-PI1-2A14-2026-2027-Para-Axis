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

    /** Opens a ticket for the logged-in user */
    public static function open(array $user, string $subject, string $message): array
    {
        return self::insert([
            'customer_name'  => $user['name'] ?? 'Customer',
            'customer_email' => $user['email'] ?? '',
            'subject'        => $subject,
            'message'        => $message,
            'status'         => 'open',
            'created_at'     => date('Y-m-d H:i:s'),
        ]);
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
