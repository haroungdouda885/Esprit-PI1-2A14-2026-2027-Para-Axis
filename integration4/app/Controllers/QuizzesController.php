<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Quiz;
use App\Models\QuizQuestion;

class QuizzesController extends Controller
{
    protected string $page   = 'quizzes';
    protected string $title  = "Quizzes | Par'Axis Back office";
    protected string $layout = 'layout';

    public function __construct()
    {
        $this->requireAdmin(); // every action of this controller is for admins
    }

    public function index(): void
    {
        $quizzes = array_reverse(Quiz::all()); // newest first
        foreach ($quizzes as &$q) {
            $q['question_count'] = count(QuizQuestion::forQuiz($q['id']));
            $q['product']        = Quiz::productOf($q);
        }
        unset($q);

        $this->render('quizzes', ['quizzes' => $quizzes, 'products' => Product::all()]);
    }

    public function create(): void
    {
        if ($this->isPost()) {
            $title = trim($_POST['title'] ?? '');
            if ($title !== '') {
                $productId = (int) ($_POST['product_id'] ?? 0);
                Quiz::insert([
                    'product_id'  => $productId && Product::find($productId) ? $productId : null, // optional link
                    'title'       => $title,
                    'category'    => trim($_POST['category'] ?? '') ?: 'General',
                    'emoji'       => trim($_POST['emoji'] ?? '') ?: '🧠',
                    'active'      => isset($_POST['active']) ? 1 : 0,
                    'created_at'  => date('Y-m-d H:i'),
                    'description' => trim($_POST['description'] ?? ''),
                ]);
            }
        }
        $this->redirect('quizzes');
    }

    /** Active <-> Inactive */
    public function status(): void
    {
        if ($this->isPost()) {
            $active = ($_POST['active'] ?? '0') === '1' ? 1 : 0;
            Quiz::update((int) ($_POST['quiz_id'] ?? 0), ['active' => $active]);
        }
        $this->redirect('quizzes');
    }

    public function delete(): void
    {
        if ($this->isPost()) {
            Quiz::deleteWithQuestions((int) ($_POST['quiz_id'] ?? 0));
        }
        $this->redirect('quizzes');
    }
}
