<?php

namespace App\Controllers;

use App\Models\Quiz;
use App\Models\QuizQuestion;

/** Front office of the Health quiz module: list, play one quiz, see the score. */
class HealthQuizController extends Controller
{
    protected string $page   = 'quiz';
    protected string $title  = "Health quiz | Par'Axis";
    protected string $layout = 'front-layout';

    public function index(): void
    {
        $quizzes = Quiz::active();
        foreach ($quizzes as &$q) {
            $q['question_count'] = count(QuizQuestion::forQuiz($q['id']));
        }
        unset($q);

        $this->render('quiz', ['quizzes' => $quizzes]);
    }

    public function show(): void
    {
        $quiz = Quiz::find((int) ($_GET['id'] ?? 0));
        if (!$quiz || !$quiz['active']) {
            $this->redirect('quiz');
        }

        // the quiz page belongs to the "Quiz" menu ($page stays 'quiz')
        $this->title = $quiz['title'] . " | Par'Axis";

        $this->render('quiz-play', [
            'quiz'      => $quiz,
            'questions' => QuizQuestion::forQuiz($quiz['id']),
        ]);
    }

    /** Computes the score (nothing is stored) and shows the result */
    public function submit(): void
    {
        $quizId = (int) ($_POST['quiz_id'] ?? 0);
        $quiz   = Quiz::find($quizId);

        if (!$this->isPost() || !$quiz || !$quiz['active']) {
            $quiz ? $this->redirect('quiz-play', ['id' => $quizId]) : $this->redirect('quiz');
        }

        $answers = $_POST['answers'] ?? [];
        $score   = 0;
        $results = [];
        foreach (QuizQuestion::forQuiz($quizId) as $q) {
            $given = $answers[$q['id']] ?? '';
            $ok    = $given === $q['correct_option'];
            if ($ok) {
                $score++;
            }
            $results[] = [
                'question' => $q['question'],
                'ok'       => $ok,
                'answer'   => $q['option_' . $q['correct_option']],
            ];
        }

        $this->title = $quiz['title'] . " | Par'Axis";
        $this->render('quiz-result', ['quiz' => $quiz, 'score' => $score, 'results' => $results]);
    }
}
