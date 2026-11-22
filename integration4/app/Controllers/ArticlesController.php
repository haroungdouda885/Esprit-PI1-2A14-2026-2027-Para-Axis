<?php

namespace App\Controllers;

use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\Event;

class ArticlesController extends Controller
{
    protected string $page   = 'articles';
    protected string $title  = "Articles | Par'Axis Back office";
    protected string $layout = 'layout';

    public function __construct()
    {
        $this->requireAdmin(); // every action of this controller is for admins
    }

    public function index(): void
    {
        $articles = array_reverse(Article::all()); // newest first
        foreach ($articles as &$a) {
            $a['comment_count'] = count(ArticleComment::where('article_id', $a['id']));
            $a['event']         = Article::eventOf($a);
        }
        unset($a);

        $this->render('articles', ['articles' => $articles, 'events' => Event::all()]);
    }

    public function create(): void
    {
        if ($this->isPost()) {
            $title   = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            if ($title !== '' && $content !== '') {
                $eventId = (int) ($_POST['event_id'] ?? 0);
                Article::insert([
                    'event_id'   => $eventId && Event::find($eventId) ? $eventId : null, // optional link
                    'title'      => $title,
                    'author'     => trim($_POST['author'] ?? '') ?: 'Pharmacy team',
                    'emoji'      => trim($_POST['emoji'] ?? '') ?: '📝',
                    'published'  => isset($_POST['published']) ? 1 : 0,
                    'created_at' => date('Y-m-d H:i'),
                    'content'    => $content,
                ]);
            }
        }
        $this->redirect('articles');
    }

    /** Published <-> Draft */
    public function status(): void
    {
        if ($this->isPost()) {
            $published = ($_POST['published'] ?? '0') === '1' ? 1 : 0;
            Article::update((int) ($_POST['article_id'] ?? 0), ['published' => $published]);
        }
        $this->redirect('articles');
    }

    public function delete(): void
    {
        if ($this->isPost()) {
            Article::deleteWithComments((int) ($_POST['article_id'] ?? 0));
        }
        $this->redirect('articles');
    }
}
