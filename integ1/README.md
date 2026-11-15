# Par'Axis – Online Parapharmacy (ParaSanté)

## Overview

This project was developed as part of the **Web Technologies Project (PW) – 2nd Year Engineering Program** at
**Esprit School of Engineering** (Academic Year 2026–2027).

Par'Axis is an online parapharmacy web application. It brings together, in **one PHP MVC application**, a
front office for customers and a back office for administrators. This repository contains the integration of
two team modules built during the *Template Integration* workshop:

- **Customer support** (member 1): customers open support tickets and chat with the support team.
- **Health advice** (member 4): health articles with customer comments, managed from the back office.

Problem solved: customers get health advice and a support channel in the same store, and admins manage both
from a single dashboard. The other modules (shop, orders, quiz, events) are already routed as empty pages and
will be integrated the same way.

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
- Health advice: list of published articles, article page, post a comment (logged-in users).
- Customer support: open a ticket, follow the conversation, reply while the ticket is not closed.
- Sign up / log in / log out (sign-up always creates a *customer* account).
- Navbar with the active link highlighted automatically.

**Back office (admins only)**
- Support tickets: reply to the customer, change status (open / answered / closed), delete a ticket and its conversation.
- Articles: add, publish / unpublish (draft), delete (its comments are deleted too).
- Comments: list and delete.
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
app/helpers.php                    isActive(), groupIsActive(), e(), currentUser(), loginUser()
app/Controllers/
  Controller.php                   base: render(), capture(), redirect(), isPost(), requireAdmin()
  AuthController.php               login, signup, logout                      (shared)
  SupportController.php            FRONT – support tickets                    (member 1)
  AdminController.php              BACK  – support tickets                    (member 1)
  HealthAdviceController.php       FRONT – articles + comments                (member 4)
  ArticlesController.php           BACK  – articles                           (member 4)
  CommentsController.php           BACK  – comments                           (member 4)
  PlaceholderController.php        empty pages for the other members' modules
app/Models/
  Model.php                        all(), find(), where(), insert(), update(), delete()
  User.php                         admin account + sign-up accounts
  Ticket.php, TicketReply.php      ticket_reply.ticket_id -> ticket.id        (member 1)
  Article.php, ArticleComment.php  article_comment.article_id -> article.id   (member 4)
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
3. Open http://localhost:8000/index.php?page=health-advice

*With XAMPP:* copy the folder into `htdocs`, start Apache, then open
`http://localhost/<folder>/index.php?page=health-advice`.

## Usage

### Accounts (no database yet)

| Account  | Email                  | Password             | Goes to                       |
|----------|------------------------|----------------------|-------------------------------|
| Admin    | `admin@parasante.demo` | `Admin123!`          | `?page=articles` (back office)|
| Customer | create one with **Sign up** | 8 characters min. | `?page=health-advice`         |

### Test scenario
1. **Sign up** as a customer → **Support**: open a ticket, add a reply.
2. **Health advice**: open an article, post a comment.
3. **Log out**, log in as **admin** → **Support tickets**: answer the ticket, close it, delete it.
4. **Comments**: the customer's comment is there; delete it. **Articles**: add / publish / delete.

> Data is stored in the PHP session (`tickets`, `ticket_replies`, `articles`, `article_comments`).
> Use the same browser for the customer and the admin; closing the browser resets the data.

### Add a page (workshop step 11)
1. Create `app/Views/pages/<name>.php` with the page content only.
2. Create `app/Controllers/<Name>Controller.php` extending `Controller` (set `$page`, `$title`, `$layout`) and call `$this->render('<name>')`.
3. Point the route in `app/routes.php` to the new controller.

## Contributors

| Member   | Module                         |
|----------|--------------------------------|
| Member 1 – Haroun gdouda | Customer support (tickets,replys) |
| Member 2 – *[name]* | Catalog                    |
| Member 3 – *[name]* | Sales / orders             |
| Member 4 – Mariem Sammar | Health advice (articles, comments) |
| Member 5 – *[name]* | Quiz                       |
| Member 6 – *[name]* | Events                     |

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
