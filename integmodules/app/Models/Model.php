<?php

namespace App\Models;

/**
 * Base model: "Represents the data contained in the classes."
 * No database yet: each child class writes its starting rows in $rows,
 * then the rows are copied into the PHP session so we can add / edit / delete them.
 * Later, only these methods change: they will run SQL queries (PDO) instead.
 */
abstract class Model
{
    /** Name of the "table" (key in $_SESSION) — defined in each child class */
    protected static string $table = '';

    /** Starting rows of the "table" (defined in each child class) */
    protected static array $rows = [];

    public static function all(): array
    {
        if (!isset($_SESSION[static::$table])) {
            $_SESSION[static::$table] = static::$rows; // first visit: load the starting rows
        }
        return $_SESSION[static::$table];
    }

    public static function find(int $id): ?array
    {
        foreach (static::all() as $row) {
            if ((int) $row['id'] === $id) {
                return $row;
            }
        }
        return null;
    }

    /** Rows where $field equals $value */
    public static function where(string $field, $value): array
    {
        return array_values(array_filter(static::all(), fn($row) => ($row[$field] ?? null) === $value));
    }

    /** INSERT: adds a row with a new id and returns it */
    public static function insert(array $data): array
    {
        $rows = static::all();
        $data = ['id' => $rows ? max(array_column($rows, 'id')) + 1 : 1] + $data;
        $_SESSION[static::$table][] = $data;
        return $data;
    }

    /** UPDATE ... WHERE id = $id */
    public static function update(int $id, array $data): void
    {
        static::all();
        foreach ($_SESSION[static::$table] as &$row) {
            if ((int) $row['id'] === $id) {
                $row = array_merge($row, $data);
                break;
            }
        }
        unset($row);
    }

    /** DELETE ... WHERE id = $id */
    public static function delete(int $id): void
    {
        $_SESSION[static::$table] = array_values(array_filter(static::all(), fn($row) => (int) $row['id'] !== $id));
    }
}
