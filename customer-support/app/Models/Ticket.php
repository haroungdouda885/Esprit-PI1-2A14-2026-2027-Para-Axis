<?php

namespace App\Models;

/**
 * Support ticket. Temporary storage in the PHP session (no database yet).
 * Later: replace the session operations with PDO queries.
 */
class Ticket
{
    public static function all(): array
    {
        return $_SESSION['tickets'] ?? [];
    }

    public static function find(int $id): ?array
    {
        foreach (self::all() as $ticket) {
            if ((int) ($ticket['id'] ?? 0) === $id) {
                return $ticket;
            }
        }
        return null;
    }

    public static function forCustomer(string $email): array
    {
        return array_values(array_filter(self::all(), fn($t) => ($t['customer_email'] ?? '') === $email));
    }

    public static function create(array $data): void
    {
        $_SESSION['tickets'] ??= [];
        $id = count($_SESSION['tickets']) ? max(array_column($_SESSION['tickets'], 'id')) + 1 : 1;
        $_SESSION['tickets'][] = [
            'id'             => $id,
            'customer_name'  => $_SESSION['demo_user']['name'] ?? 'Demo Customer',
            'customer_email' => $_SESSION['demo_user']['email'] ?? 'customer@example.test',
            'subject'        => $data['subject'],
            'message'        => $data['message'],
            'status'         => 'open',
            'created_at'     => date('Y-m-d H:i:s'),
        ];
    }

    public static function updateStatus(int $id, string $status): void
    {
        $_SESSION['tickets'] ??= [];
        foreach ($_SESSION['tickets'] as &$ticket) {
            if ((int) ($ticket['id'] ?? 0) === $id) {
                $ticket['status'] = $status;
                break;
            }
        }
        unset($ticket);
    }

    public static function delete(int $id): void
    {
        $_SESSION['tickets'] = array_values(array_filter($_SESSION['tickets'] ?? [], fn($t) => (int) ($t['id'] ?? 0) !== $id));
        $_SESSION['ticket_replies'] = array_values(array_filter($_SESSION['ticket_replies'] ?? [], fn($r) => (int) ($r['ticket_id'] ?? 0) !== $id));
    }
}
