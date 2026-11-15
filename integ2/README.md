# Par'Axis – Online Parapharmacy (ParaSanté)

## Overview

This project was developed as part of the **Web Technologies Project (PW) – 2nd Year Engineering Program** at
**Esprit School of Engineering** (Academic Year 2026–2027).

Par'Axis is an online parapharmacy web application. It brings together, in **one PHP MVC application**, a
front office for customers and a back office for administrators. This repository contains the integration of
two team modules built during the *Template Integration* workshop:

- **Health quiz** (member 5): short health quizzes that customers answer to get a score and the correct answers.
- **Events & workshops** (member 6): in-store events that customers register for, managed from the back office.

Problem solved: customers test their health knowledge and sign up for workshops in the same store, and admins
manage quizzes, questions, events and registrations from a single dashboard. The other modules (support,
catalog, orders, health advice) are already routed as empty pages and will be integrated the same way.

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
- [Getting Started](#getting-started)
- [Usage](#usage)
- [Contributors](#contributors)
- [Academic Context](#academic-context)
- [Contributing](#contributing)
- [License](#license)
- [Acknowledgments](#acknowledgments)

## Features

**Front office**
- Health quiz: list of active quizzes, answer the questions, score + correct answers (computed by PHP, nothing stored).
- Events: filters Upcoming / Past / All, search, seats bar on each card, register (logged-in customers).
- My registrations: list of the customer's registrations, cancel one (the seat is released).
- Sign up / log in / log out (sign-up always creates a *customer* account).
- Navbar with the active link highlighted automatically.

**Back office (admins only)**
- Quizzes: add, switch active / inactive (inactive quizzes disappear from the front), delete (its questions too).
- Quiz questions: add a question to a quiz, delete a question.
- Events: create, edit (capacity can't go below the seats taken), delete (its registrations too).
- Registrations: filter by event / status, cancel / re-register (capacity is checked), delete.
- Sidebar with the active link highlighted automatically; non-admins are redirected to the login page.

## Tech Stack

### Frontend
- HTML5, CSS3 (`front.css`, `admin.css`)
- JavaScript (vanilla – `main.js`)
- Google Fonts (DM Sans, Manrope)

### Backend
- PHP 8 (MVC architecture, no framework)
- PHP sessions for temporary storage (the shared MySQL database will replace them – only `Model.php` will change)

## Architecture

One entry point for the front office **and** the back office: `index.php?page=<key>`.

```
index.php                          front controller + autoloader
app/routes.php                     page key => [Controller, method]
app/helpers.php                    isActive(), groupIsActive(), e(), currentUser(), loginUser(),
                                   isAdmin(), fmtDate(), flash(), takeFlash()
app/Controllers/
  Controller.php                   base: render(), capture(), redirect(), isPost(), requireAdmin()
  AuthController.php               login, signup, logout (?next=my-registrations)          (shared)
  HealthQuizController.php         FRONT – quiz list, play, result                         (member 5)
  QuizzesController.php            BACK  – quizzes                                         (member 5)
  QuizQuestionsController.php      BACK  – quiz questions                                  (member 5)
  EventsController.php             FRONT – events, register, my registrations, cancel      (member 6)
  EventsAdminController.php        BACK  – events                                          (member 6)
  RegistrationsController.php      BACK  – registrations                                   (member 6)
  PlaceholderController.php        empty pages for the other members' modules
app/Models/
  Model.php                        all(), find(), where(), insert(), update(), delete()
  User.php                         admin account + sign-up accounts
  Quiz.php, QuizQuestion.php       quiz_question.quiz_id -> quiz.id                        (member 5)
  Event.php, EventRegistration.php event_registration.event_id -> event.id,
                                   event_registration.user_id -> user.id                   (member 6)
app/Views/
  front-layout.php                 head, spinner, navbar, content, footer, scripts
  layout.php                       head (+ preloader), sidebar, header, content, footer, scripts
  includes/                        front office partials (Part 1 of the workshop)
  partials/                        back office partials  (Part 2 of the workshop)
  pages/                           page content only (no require_once)
  assets/css, assets/js
```

## Getting Started

### Prerequisites
- **PHP 8.0+** – download it from [php.net](https://www.php.net/downloads) or install **XAMPP / WAMP**.

### Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/<organisation>/Esprit-PW-<Classe>-2026-Paraxis.git
   cd Esprit-PW-<Classe>-2026-Paraxis
   ```
2. Start the PHP server:
   ```bash
   php -S localhost:8000
   ```
3. Open http://localhost:8000/index.php?page=events

*With XAMPP:* copy the folder into `htdocs`, start Apache, then open
`http://localhost/<folder>/index.php?page=events`.

## Usage

### Accounts (no database yet)

| Account  | Email                  | Password             | Goes to                            |
|----------|------------------------|----------------------|------------------------------------|
| Admin    | `admin@parasante.demo` | `Admin123!`          | `?page=events-admin` (back office) |
| Customer | create one with **Sign up** | 8 characters min. | `?page=events`                   |

### Test scenario
1. **Quiz**: pick a quiz → answer the questions → **See my score** (score and correct answers).
2. **Sign up** as a customer → **Events**: filter, search, **Register** → the card shows "✓ You are registered".
3. **My registrations**: cancel a registration → the seat is released.
4. **Log out**, log in as **admin** → **Quizzes**: add a quiz, switch it inactive (it disappears from the front), delete one.
5. **Quiz questions**: add a question → on the front, the quiz has one more question.
6. **Events**: create / edit / delete an event. **Registrations**: filter, cancel / re-register
   (the full "Sun protection masterclass" refuses), delete.

> Data is stored in the PHP session (`quizzes`, `quiz_questions`, `events`, `event_registrations`).
> Use the same browser for the customer and the admin; closing the browser resets the data.

### Add a page (workshop step 11)
1. Create `app/Views/pages/<name>.php` with the page content only.
2. Create `app/Controllers/<Name>Controller.php` extending `Controller` (set `$page`, `$title`, `$layout`) and call `$this->render('<name>')`.
3. Point the route in `app/routes.php` to the new controller.

## Contributors

| Member   | Module                         |
|----------|--------------------------------|
| Member 1 – *[name]* | Customer support (tickets) |
| Member 2 – *[name]* | Catalog                    |
| Member 3 – *[name]* | Sales / orders             |
| Member 4 – *[name]*| Health advice (articles, comments) |
| Member 5 – Elyes B.Dabbous| Health quiz (quizzes, questions) |
| Member 6 – Ayoub Dellai| Events (events, registrations) |

## Academic Context

Developed at **Esprit School of Engineering – Tunisia**
Web Technologies Project (PW) – 2A | 2026–2027 | Team **Axis**

## Contributing

1. Fork the repository and create a branch: `git checkout -b feature/<module-name>`
2. Commit your changes: `git commit -m "Add <module-name> module"`
3. Push the branch: `git push origin feature/<module-name>`
4. Open a **Pull Request**.

## License

This project is distributed under the **MIT License** – it can be used, modified and redistributed with
attribution. It is an academic project of **Esprit School of Engineering**.

## Acknowledgments

- **Esprit School of Engineering** and the Web Technologies Project teaching team.
- Workshop *Template Integration* (Web Technologies Project 2026–2027).
