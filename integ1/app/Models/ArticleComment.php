<?php

namespace App\Models;

/** Entity ArticleComment — linked to Article by article_id */
class ArticleComment extends Model
{
    protected static string $table = 'article_comments';

    protected static array $rows = [
        ['id' => 1, 'article_id' => 1, 'author' => 'Sarah K.',   'content' => 'Very useful, thank you!',                          'created_at' => '2026-11-03 18:20'],
        ['id' => 2, 'article_id' => 1, 'author' => 'Mohamed A.', 'content' => 'Which moisturizer do you recommend for oily skin?', 'created_at' => '2026-11-04 08:45'],
        ['id' => 3, 'article_id' => 2, 'author' => 'Lina N.',    'content' => 'I did not know about the 20 minutes, thanks.',      'created_at' => '2026-11-07 12:10'],
    ];

    /** Comments of one article (front office) */
    public static function forArticle(int $articleId): array
    {
        return array_values(array_filter(self::all(), fn($c) => (int) $c['article_id'] === $articleId));
    }
}
