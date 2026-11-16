<?php

namespace App\Models;

/**
 * Entity TicketReply: a message in a ticket conversation (customer or admin).
 * Uses the shared Model base (session storage until the team database exists).
 */
class TicketReply extends Model
{
    protected static string $table = 'ticket_replies';

    protected static array $rows = [];

    public static function add(int $ticketId, string $message, string $role): array
    {
        return self::insert([
            'ticket_id'   => $ticketId,
            'message'     => $message,
            'author_role' => $role,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    public static function forTicket(int $id): array
    {
        return array_values(array_filter(self::all(), fn($r) => (int) ($r['ticket_id'] ?? 0) === $id));
    }
}
