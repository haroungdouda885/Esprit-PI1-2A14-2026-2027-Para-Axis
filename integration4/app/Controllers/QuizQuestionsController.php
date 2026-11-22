<?php

namespace App\Controllers;

use App\Models\Quiz;
use App\Models\QuizQuestion;

class QuizQuestionsController extends Controller
{
    protected string $page   = 'quiz-questions';
    protected string $title  = "Quiz questions | Par'Axis Back office";
    protected string $layout = 'layout';

    public function __construct()
    {
        $this->requireAdmin(); // every action of this controller is for admins
    }

    public function index(): void
    {
        $questions = array_reverse(QuizQuestion::all()); // newest first
        foreach ($questions as &$q) {
            $q['quiz_title'] = Quiz::find($q['quiz_id'])['title'] ?? '—'; // join quiz_id -> quiz
        }
        unset($q);

        $this->render('quiz-questions', ['questions' => $questions, 'quizzes' => Quiz::all()]);
    }

    public function create(): void
    {
        if ($this->isPost()) {
            $quizId   = (int) ($_POST['quiz_id'] ?? 0);
            $question = trim($_POST['question'] ?? '');
            $options  = [];
            foreach (QuizQuestion::OPTIONS as $o) {
                $options['option_' . $o] = trim($_POST['option_' . $o] ?? '');
            }
            $correct = $_POST['correct_option'] ?? '';

            if (Quiz::find($quizId) && $question !== '' && !in_array('', $options, true)
                && in_array($correct, QuizQuestion::OPTIONS, true)) {
                QuizQuestion::insert(['quiz_id' => $quizId, 'question' => $question] + $options + ['correct_option' => $correct]);
            }
        }
        $this->redirect('quiz-questions');
    }

    public function delete(): void
    {
        if ($this->isPost()) {
            QuizQuestion::delete((int) ($_POST['question_id'] ?? 0));
        }
        $this->redirect('quiz-questions');
    }
}
