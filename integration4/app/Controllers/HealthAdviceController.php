<?php

namespace App\Controllers;

use App\Models\Article;
use App\Models\ArticleComment;

/** Front office of the Health advice module: list, one article + its comments, post a comment. */
class HealthAdviceController extends Controller
{
    protected string $page   = 'health-advice';
    protected string $title  = "Health advice | Par'Axis";
    protected string $layout = 'front-layout';

    public function index(): void
    {
        $this->render('health-advice', ['articles' => Article::published()]);
    }

    public function show(): void
    {
        $article = Article::find((int) ($_GET['id'] ?? 0));
        if (!$article || !$article['published']) {
            $this->redirect('health-advice');
        }

        // the article page belongs to the "Health advice" menu ($page stays 'health-advice')
        $this->title = $article['title'] . " | Par'Axis";

        $this->render('article', [
            'article'  => $article,
            'event'    => Article::eventOf($article),
            'comments' => ArticleComment::forArticle($article['id']),
            'user'     => currentUser(),
            'sent'     => isset($_GET['sent']),
        ]);
    }

    public function comment(): void
    {
        $articleId = (int) ($_POST['article_id'] ?? 0);
        $article   = Article::find($articleId);
        $user      = currentUser();
        $content   = trim($_POST['content'] ?? '');

        if ($this->isPost() && $user && $article && $article['published'] && $content !== '') {
            // the comment is visible right away (the admin can delete it in the back office)
            ArticleComment::insert([
                'article_id' => $articleId,
                'user_id'    => $user['id'],
                'author'     => $user['name'],
                'content'    => $content,
                'created_at' => date('Y-m-d H:i'),
            ]);
            $this->redirect('article', ['id' => $articleId, 'sent' => 1]);
        }

        $article ? $this->redirect('article', ['id' => $articleId]) : $this->redirect('health-advice');
    }
}
