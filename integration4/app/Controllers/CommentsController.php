<?php

namespace App\Controllers;

use App\Models\Article;
use App\Models\ArticleComment;

class CommentsController extends Controller
{
    protected string $page   = 'comments';
    protected string $title  = "Comments | Par'Axis Back office";
    protected string $layout = 'layout';

    public function __construct()
    {
        $this->requireAdmin(); // every action of this controller is for admins
    }

    public function index(): void
    {
        $comments = array_reverse(ArticleComment::all()); // newest first
        foreach ($comments as &$c) {
            $c['article_title'] = Article::find($c['article_id'])['title'] ?? '—'; // join article_id -> article
        }
        unset($c);

        $this->render('comments', ['comments' => $comments]);
    }

    public function delete(): void
    {
        if ($this->isPost()) {
            ArticleComment::delete((int) ($_POST['comment_id'] ?? 0));
        }
        $this->redirect('comments');
    }
}
