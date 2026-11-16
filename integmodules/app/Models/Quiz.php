<?php

namespace App\Models;

/** Entity Quiz (module Health quiz) — one Quiz has many QuizQuestion (quiz_question.quiz_id) */
class Quiz extends Model
{
    protected static string $table = 'quizzes';

    protected static array $rows = [
        [
            'id' => 1, 'title' => 'Sun care basics', 'category' => 'Sun care', 'emoji' => '☀️', 'active' => 1,
            'created_at' => '2026-11-02 10:00',
            'description' => 'Test what you know about protecting your skin.',
        ],
        [
            'id' => 2, 'title' => 'Hydrated skin', 'category' => 'Skin care', 'emoji' => '💧', 'active' => 1,
            'created_at' => '2026-11-06 14:30',
            'description' => 'Three questions about keeping your skin hydrated every day.',
        ],
        [
            'id' => 3, 'title' => 'Winter lip care', 'category' => 'Lip care', 'emoji' => '💋', 'active' => 0,
            'created_at' => '2026-11-10 09:15',
            'description' => 'Draft quiz: questions are still being written.',
        ],
    ];

    /** Only active quizzes (front office) */
    public static function active(): array
    {
        return self::where('active', 1);
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
