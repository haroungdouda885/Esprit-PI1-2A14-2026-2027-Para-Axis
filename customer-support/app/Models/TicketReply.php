<?php

namespace App\Models;

/**
 * Reply in a ticket conversation (customer or admin). Temporary session storage.
 */
class TicketReply
{
    public static function all(): array
    {
        return $_SESSION['ticket_replies'] ?? [];
    }

    public static function add(int $ticketId, string $message, string $role): void
    {
        $_SESSION['ticket_replies'] ??= [];
        $_SESSION['ticket_replies'][] = [
            'ticket_id'   => $ticketId,
            'message'     => $message,
            'author_role' => $role,
            'created_at'  => date('Y-m-d H:i:s'),
        ];
    }

    public static function forTicket(int $id): array
    {
        return array_values(array_filter(self::all(), fn($r) => (int) ($r['ticket_id'] ?? 0) === $id));
    }
}
