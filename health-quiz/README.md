# Par'Axis — Health quiz module 

Module entities: **Quiz** + **QuizQuestion** (`quiz_question.quiz_id` → `quiz.id`).
Same architecture as the team's *health-advice* and *customer-support* modules: **one entry point** (`index.php?page=...`)
for the front office AND the back office, routes `page => [Controller, method]`, two layouts,
page views without any `require_once`. No database yet.
The layouts, partials, includes, CSS and JS are the **same files as health-advice**; only the module pages change.

## Structure

```
index.php                          front controller + autoloader (index.php?page=...)
app/routes.php                     page key => [Controller, method]
app/helpers.php                    isActive(), groupIsActive(), e(), currentUser(), loginUser()
app/Controllers/
  Controller.php                   abstract base: render(), capture(), redirect(), isPost(), requireAdmin()
  HealthQuizController.php         FRONT  : index (list), show (play a quiz), submit (score)
  AuthController.php               login, signup, logout
  QuizzesController.php            BACK   : index, create, status (active/inactive), delete
  QuizQuestionsController.php      BACK   : index, create, delete
  PlaceholderController.php        empty pages for the other team members
app/Models/
  Model.php                        all(), find(), where(), insert(), update(), delete() — session storage
  Quiz.php, QuizQuestion.php       starting rows + active(), forQuiz(), deleteWithQuestions()
  User.php                         admin account + sign-up accounts
app/Views/
  front-layout.php                 front office: head, spinner, navbar, content, footer, scripts
  layout.php                       back office : head (+ preloader), sidebar, header, content, footer, scripts
  includes/                        PART 1: head.php, spinner.php, navbar.php, footer.php (+ back to top), scripts.php
  partials/                        PART 2: head.php, sidebar.php, header.php, footer.php, scripts.php
  pages/                           page content only: quiz, quiz-play, quiz-result, login, signup,
                                   quizzes, quiz-questions, placeholder
  assets/css/front.css, admin.css, assets/js/main.js
```

## Run
- `php -S localhost:8000` in this folder, then open http://localhost:8000/index.php?page=quiz
- or XAMPP: copy the folder into `htdocs`, start Apache, open
  http://localhost/paraxis-health-quiz/index.php?page=quiz

## Accounts (no database yet)

| Account | Email | Password | Goes to |
|---|---|---|---|
| Admin | `admin@parasante.demo` | `Admin123!` | `?page=quizzes` (back office) |
| Customer | create one with **Sign up** | 8 characters min. | `?page=quiz` |

Back-office pages call `requireAdmin()`: anyone else is sent to `?page=login&admin=1`.

## Scenario to test
1. Open **Quiz** → pick a quiz → answer the questions → **See my score**: the score and the correct answers are shown
   (the score is computed by PHP, nothing is stored).
2. Log in as admin → **Quiz questions**: add a question to a quiz → back on the front, the quiz has one more question.
3. **Quizzes**: add a quiz, switch Active/Inactive (inactive quizzes disappear from the front), delete one
   (its questions are deleted too).

Data is kept in the PHP session (`$_SESSION['quizzes']`, `$_SESSION['quiz_questions']`), starting
from the rows written in the models. Close the browser to reset.



## Add a page
1. Create `app/Views/pages/<name>.php` with the page content only.
2. Create `app/Controllers/<Name>Controller.php` extending `Controller` (set `$page`, `$title`, `$layout`)
   and call `$this->render('<name>')`.
3. Add (or point) the route in `app/routes.php`.

