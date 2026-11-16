<?php

namespace App\Models;

/** Entity Event (module Events) — one Event has many EventRegistration (event_registration.event_id) */
class Event extends Model
{
    protected static string $table = 'events';

    protected static array $rows = [
        ['id' => 1, 'title' => 'Free skin consultation day',  'description' => 'Meet our pharmacist for a free skin check.',          'location' => "Par'Axis store, Tunis",  'event_date' => '2026-12-05T10:00', 'capacity' => 20, 'active' => 1, 'created_at' => '2026-11-01 09:00:00'],
        ['id' => 2, 'title' => 'Healthy hair workshop',       'description' => 'Learn a simple, gentle hair routine.',                 'location' => "Par'Axis store, Tunis",  'event_date' => '2026-12-19T15:00', 'capacity' => 12, 'active' => 1, 'created_at' => '2026-11-01 09:05:00'],
        ['id' => 3, 'title' => 'Back-to-school hygiene talk', 'description' => 'Simple hygiene habits for children and parents.',      'location' => "Par'Axis store, Tunis",  'event_date' => '2026-10-02T16:00', 'capacity' => 15, 'active' => 1, 'created_at' => '2026-09-15 09:00:00'],
        ['id' => 4, 'title' => 'Sun protection masterclass',  'description' => 'Choose and apply your sun care the right way.',        'location' => "Par'Axis store, Sousse", 'event_date' => '2026-12-12T11:00', 'capacity' => 3,  'active' => 1, 'created_at' => '2026-11-02 10:00:00'],
    ];

    /** Picture of the card: emoji chosen from the title / description */
    private const LOOKS = [
        '/skin|consult/i' => '🧴', '/hair/i' => '💆', '/sun/i' => '☀️',
        '/hygiene|school|oral/i' => '🪥', '/nutrition|diet|food/i' => '🥗',
    ];
    private const COLORS = ['#e8f1e9', '#f8efd8', '#eaf1f5', '#f1e8ed', '#e4f1f0', '#edf1df'];

    public static function emojiOf(array $e): string
    {
        foreach (self::LOOKS as $re => $emoji) {
            if (preg_match($re, $e['title'] . ' ' . ($e['description'] ?? ''))) {
                return $emoji;
            }
        }
        return '📅';
    }

    public static function colorOf(array $e): string
    {
        return self::COLORS[(int) $e['id'] % count(self::COLORS)];
    }

    /** The event date is before now */
    public static function isPast(array $e): bool
    {
        return strtotime($e['event_date']) < time();
    }

    /** Seats taken = registrations with status "registered" only */
    public static function taken(int $id): int
    {
        return count(array_filter(EventRegistration::forEvent($id), fn($r) => $r['status'] === 'registered'));
    }

    public static function isFull(array $e): bool
    {
        return self::taken((int) $e['id']) >= (int) $e['capacity'];
    }

    /**
     * Front-office list: visible events only, filter upcoming / past / all + text search
     * (title, description, location). Upcoming and all: soonest first; past: latest first.
     */
    public static function search(string $filter = 'upcoming', string $q = ''): array
    {
        $q    = mb_strtolower(trim($q));
        $list = array_values(array_filter(self::all(), function ($e) use ($filter, $q) {
            if ((int) $e['active'] !== 1) {
                return false;
            }
            if ($filter !== 'all' && ($filter === 'past') !== self::isPast($e)) {
                return false;
            }
            return $q === '' || str_contains(mb_strtolower($e['title'] . ' ' . ($e['description'] ?? '') . ' ' . ($e['location'] ?? '')), $q);
        }));
        usort($list, fn($a, $b) => $filter === 'past'
            ? strtotime($b['event_date']) <=> strtotime($a['event_date'])
            : strtotime($a['event_date']) <=> strtotime($b['event_date']));
        return $list;
    }

    /** Back-office status pill: [label, CSS class] */
    public static function status(array $e): array
    {
        if ((int) $e['active'] !== 1) {
            return ['Hidden', 'shipped'];
        }
        if (self::isPast($e)) {
            return ['Finished', 'shipped'];
        }
        return self::isFull($e) ? ['Full', 'pending'] : ['Open', 'delivered'];
    }

    /** Deletes the event and its registrations (ON DELETE CASCADE) */
    public static function deleteWithRegistrations(int $id): void
    {
        foreach (EventRegistration::forEvent($id) as $r) {
            EventRegistration::delete((int) $r['id']);
        }
        self::delete($id);
    }
}
