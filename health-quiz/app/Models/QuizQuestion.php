<?php

namespace App\Models;

/** Entity QuizQuestion — linked to Quiz by quiz_id. correct_option is 'a', 'b' or 'c'. */
class QuizQuestion extends Model
{
    protected static string $table = 'quiz_questions';

    public const OPTIONS = ['a', 'b', 'c'];

    protected static array $rows = [
        ['id' => 1, 'quiz_id' => 1, 'question' => 'Which SPF is recommended for daily use?',           'option_a' => 'SPF 5',         'option_b' => 'SPF 30 or more',     'option_c' => 'SPF 2',           'correct_option' => 'b'],
        ['id' => 2, 'quiz_id' => 1, 'question' => 'How often should you reapply sunscreen outdoors?',  'option_a' => 'Every 2 hours', 'option_b' => 'Once a day',         'option_c' => 'Never',           'correct_option' => 'a'],
        ['id' => 3, 'quiz_id' => 1, 'question' => 'When should you apply sunscreen before going out?', 'option_a' => 'Right after',   'option_b' => '20 minutes before',  'option_c' => 'The day before',  'correct_option' => 'b'],
        ['id' => 4, 'quiz_id' => 2, 'question' => 'When should you moisturize your face?',             'option_a' => 'Once a week',   'option_b' => 'Morning and night',  'option_c' => 'Never',           'correct_option' => 'b'],
        ['id' => 5, 'quiz_id' => 2, 'question' => 'Which shower is better for your skin?',             'option_a' => 'Very hot',      'option_b' => 'Ice cold',           'option_c' => 'Lukewarm',        'correct_option' => 'c'],
        ['id' => 6, 'quiz_id' => 2, 'question' => 'What kind of cleanser should you use?',             'option_a' => 'A gentle one',  'option_b' => 'Dish soap',          'option_c' => 'Alcohol only',    'correct_option' => 'a'],
    ];

    /** Questions of one quiz */
    public static function forQuiz(int $quizId): array
    {
        return array_values(array_filter(self::all(), fn($q) => (int) $q['quiz_id'] === $quizId));
    }
}
