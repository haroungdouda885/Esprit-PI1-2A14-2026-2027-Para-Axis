<?php

namespace App\Models;

/** Entity Event_Registration — linked to Event by event_id and to User by user_id */
class EventRegistration extends Model
{
    protected static string $table = 'event_registrations';

    /** status: 'registered' or 'cancelled' (only "registered" rows take a seat) */
    protected static array $rows = [
        ['id' => 1, 'event_id' => 1, 'user_id' => null, 'participant_name' => 'Sarah K.',   'participant_email' => 'sarah@example.demo',   'phone' => '22 111 222', 'status' => 'registered', 'created_at' => '2026-11-03 10:12:00'],
        ['id' => 2, 'event_id' => 1, 'user_id' => null, 'participant_name' => 'Mohamed A.', 'participant_email' => 'mohamed@example.demo', 'phone' => '55 333 444', 'status' => 'registered', 'created_at' => '2026-11-04 15:40:00'],
        ['id' => 3, 'event_id' => 1, 'user_id' => null, 'participant_name' => 'Lina N.',    'participant_email' => 'lina@example.demo',    'phone' => '98 555 666', 'status' => 'cancelled',  'created_at' => '2026-11-05 09:05:00'],
        ['id' => 4, 'event_id' => 4, 'user_id' => null, 'participant_name' => 'Sarah K.',   'participant_email' => 'sarah@example.demo',   'phone' => '22 111 222', 'status' => 'registered', 'created_at' => '2026-11-06 11:30:00'],
        ['id' => 5, 'event_id' => 4, 'user_id' => null, 'participant_name' => 'Mohamed A.', 'participant_email' => 'mohamed@example.demo', 'phone' => '55 333 444', 'status' => 'registered', 'created_at' => '2026-11-07 17:20:00'],
        ['id' => 6, 'event_id' => 3, 'user_id' => null, 'participant_name' => 'Sarah K.',   'participant_email' => 'sarah@example.demo',   'phone' => '22 111 222', 'status' => 'registered', 'created_at' => '2026-09-20 14:00:00'],
    ];

    /** Registrations of one event */
    public static function forEvent(int $eventId): array
    {
        return array_values(array_filter(self::all(), fn($r) => (int) $r['event_id'] === $eventId));
    }

    /** Registrations of one customer (newest first) */
    public static function forUser(int $userId): array
    {
        $rows = array_values(array_filter(self::all(), fn($r) => (int) ($r['user_id'] ?? 0) === $userId));
        usort($rows, fn($a, $b) => $b['id'] <=> $a['id']);
        return $rows;
    }

    /** The registration (active or cancelled) of this customer for this event, or null */
    public static function mine(int $eventId, int $userId): ?array
    {
        foreach (self::all() as $r) {
            if ((int) $r['event_id'] === $eventId && (int) ($r['user_id'] ?? 0) === $userId) {
                return $r;
            }
        }
        return null;
    }

    /** Back-office list: optional event + status filters, newest first */
    public static function filter(int $eventId = 0, string $status = 'All'): array
    {
        $rows = array_values(array_filter(self::all(), fn($r) =>
            (!$eventId || (int) $r['event_id'] === $eventId) && ($status === 'All' || $r['status'] === $status)));
        return array_reverse($rows);
    }

    /** Number of registrations with status "registered" (all events) */
    public static function countRegistered(): int
    {
        return count(self::where('status', 'registered'));
    }
}
