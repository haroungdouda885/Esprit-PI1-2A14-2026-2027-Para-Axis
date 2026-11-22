<?php

namespace App\Models;

/**
 * Entity Quiz (module Health quiz) — one Quiz has many QuizQuestion (quiz_question.quiz_id).
 * New link of the class diagram: Quiz "porte sur" 0..1 Product (product_id nullable).
 */
class Quiz extends Model
{
    protected static string $table = 'quizzes';

    protected static array $rows = [
        [
            'id' => 1, 'product_id' => 3, 'title' => 'Sun care basics', 'category' => 'Sun care', 'emoji' => '☀️', 'active' => 1,
            'created_at' => '2026-11-02 10:00',
            'description' => 'Test what you know about protecting your skin.',
        ],
        [
            'id' => 2, 'product_id' => 1, 'title' => 'Hydrated skin', 'category' => 'Skin care', 'emoji' => '💧', 'active' => 1,
            'created_at' => '2026-11-06 14:30',
            'description' => 'Three questions about keeping your skin hydrated every day.',
        ],
        [
            'id' => 3, 'product_id' => null, 'title' => 'Winter lip care', 'category' => 'Lip care', 'emoji' => '💋', 'active' => 0,
            'created_at' => '2026-11-10 09:15',
            'description' => 'Draft quiz: questions are still being written.',
        ],
    ];

    /** Only active quizzes (front office) */
    public static function active(): array
    {
        return self::where('active', 1);
    }

    /** The product the quiz is about, or null (join product_id -> product) */
    public static function productOf(array $quiz): ?array
    {
        return empty($quiz['product_id']) ? null : Product::find((int) $quiz['product_id']);
    }

    /** Active quizzes about one product (product page) */
    public static function forProduct(int $productId): array
    {
        return array_values(array_filter(self::active(), fn($q) => (int) ($q['product_id'] ?? 0) === $productId));
    }

    /** ON DELETE SET NULL: a deleted product is removed from its quizzes */
    public static function detachProduct(int $productId): void
    {
        foreach (self::all() as $q) {
            if ((int) ($q['product_id'] ?? 0) === $productId) {
                self::update((int) $q['id'], ['product_id' => null]);
            }
        }
    }

    /** Deletes the quiz and its questions (quiz_question.quiz_id -> quiz.id) */
    public static function deleteWithQuestions(int $id): void
    {
        foreach (QuizQuestion::forQuiz($id) as $q) {
            QuizQuestion::delete((int) $q['id']);
        }
        self::delete($id);
    }
}
