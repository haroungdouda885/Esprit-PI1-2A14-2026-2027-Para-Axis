<?php

namespace App\Models;

/** Entity Article (module Health advice) */
class Article extends Model
{
    protected static string $table = 'articles';

    protected static array $rows = [
        [
            'id' => 1, 'title' => '5 tips for hydrated skin', 'author' => 'Pharmacy team', 'emoji' => '💧', 'published' => 1,
            'created_at' => '2026-11-02 10:00',
            'content' => 'Drink enough water, use a gentle cleanser, moisturize morning and night, protect your skin from the sun and avoid very hot showers.',
        ],
        [
            'id' => 2, 'title' => 'Choosing the right sun protection', 'author' => 'Pharmacy team', 'emoji' => '☀️', 'published' => 1,
            'created_at' => '2026-11-06 14:30',
            'content' => 'Pick an SPF of 30 or more, apply it 20 minutes before going out, and reapply every two hours and after swimming.',
        ],
        [
            'id' => 3, 'title' => 'Winter lip care', 'author' => 'Pharmacy team', 'emoji' => '💋', 'published' => 0,
            'created_at' => '2026-11-10 09:15',
            'content' => 'Draft article: use a nourishing balm, avoid licking your lips and drink enough water.',
        ],
    ];

    /** Only published articles (front office) */
    public static function published(): array
    {
        return self::where('published', 1);
    }

    /** Deletes the article and its comments (article_comment.article_id -> article.id) */
    public static function deleteWithComments(int $id): void
    {
        foreach (ArticleComment::where('article_id', $id) as $c) {
            ArticleComment::delete((int) $c['id']);
        }
        self::delete($id);
    }
}
