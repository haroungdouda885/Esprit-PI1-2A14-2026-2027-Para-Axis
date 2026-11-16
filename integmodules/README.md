# Par'Axis – Online Parapharmacy

## Overview

This project was developed as part of the **Web Technologies Project (PW) – 2nd Year Engineering Program** at
**Esprit School of Engineering** (Academic Year 2026–2027).

Par'Axis is an online parapharmacy web application. It brings together, in **one PHP MVC application**, a
front office for customers and a back office for administrators. This repository contains the integration of
the **six team modules** built during the *Template Integration* workshop:

- **Customer support** (member 1): customers open support tickets and chat with the support team.
- **Catalog** (member 2): categories and products, shop window and product pages.
- **Orders and payments** (member 3): bag, checkout with simulated payment, order history.
- **Health advice** (member 4): health articles with customer comments.
- **Health quiz** (member 5): quizzes with score and correct answers.
- **Events & workshops** (member 6): events with limited seats and customer registrations.

Problem solved: customers shop, get health advice, test their knowledge, join wellness events and contact
support in the same store, and admins manage everything from a single dashboard.

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
- Shop (catalog): products grid, category filters, search box, product page (price, brand, stock status).
- Home: hero "Your daily dose of well-being" with links to the shop and to the health advice.
- Bag: add products **without reloading the page** (fetch + JSON), change quantities (limited by the stock), item counter in the navbar.
- Checkout: delivery address + payment method (simulated card or cash on delivery).
- My orders: order history, cancel a *pending* / *processing* order (stock comes back, payment refunded / failed).
- Health advice: list of published articles, article page, post a comment (logged-in users).
- Health quiz: list of active quizzes, answer the questions, score + correct answers.
- Events: Upcoming / Past / All filters, search, seats bar, register (logged-in customers).
- My registrations: list of the customer's registrations, cancel one (the seat is released).
- Customer support: open a ticket, follow the conversation, reply while the ticket is not closed.
- Sign up / log in / log out (sign-up always creates a *customer* account); after logging in, the customer
  goes back to the page he came from (bag, my orders, events, support…).
- Navbar with the active link highlighted automatically.

**Back office (admins only)**
- Support tickets: reply, change status (open / answered / closed), delete a ticket and its conversation.
- Categories: add, delete (its products are deleted too).
- Products: add, change the stock, delete, stock status (in stock / low stock / out of stock).
- Orders: filter by status, change the order status and the payment status, delete an order.
- Articles: add, publish / unpublish (draft), delete (its comments are deleted too). Comments: list and delete.
- Quizzes: add, active / inactive, delete (its questions too). Quiz questions: add, delete.
- Events: create, edit (capacity can't go below the seats taken), delete (its registrations too).
- Registrations: filter by event / status, cancel / re-register (capacity is checked), delete.
- Sidebar with the active link highlighted automatically; non-admins are redirected to the login page.
- The admin has no shop window: Shop, Bag and My orders are hidden for him and redirect to the back office.

## Tech Stack

### Frontend
- HTML5, CSS3 (`front.css`, `admin.css`)
- JavaScript (vanilla – `main.js`)
- Google Fonts (DM Sans, Manrope)

### Backend
- PHP 8 (MVC architecture, no framework)
- PHP sessions for temporary storage (the shared MySQL database will replace them – only `Model.php` will change)

## Architecture

One entry point for the front office **and** the back office: `index.php?page=<key>` (default page: `home`).

```
index.php                          front controller + autoloader
app/routes.php                     page key => [Controller, method]   (all six modules)
app/helpers.php                    isActive(), e(), currentUser(), loginUser(), isAdmin(),
                                   money(), fmtDate(), flash(), takeFlash()
app/Controllers/
  Controller.php                   base: render(), capture(), redirect(), isPost(), requireAdmin(), blockAdmin()
  AuthController.php               login, signup, logout (?next=<page>)                     (shared)
  HomeController.php               FRONT – home page (hero)                                 (shared)
  PlaceholderController.php        BACK  – Overview (still empty)                           (shared)
  SupportController.php            FRONT – support tickets                                  (member 1)
  AdminController.php              BACK  – support tickets                                  (member 1)
  ShopController.php               FRONT – shop, product page, add to bag                   (member 2)
  CategoriesController.php         BACK  – categories                                       (member 2)
  ProductsController.php           BACK  – products                                         (member 2)
  BagController.php                FRONT – bag + checkout                                   (member 3)
  MyOrdersController.php           FRONT – my orders                                        (member 3)
  OrdersController.php             BACK  – orders + payments                                (member 3)
  HealthAdviceController.php       FRONT – articles + comments                              (member 4)
  ArticlesController.php           BACK  – articles                                         (member 4)
  CommentsController.php           BACK  – comments                                         (member 4)
  HealthQuizController.php         FRONT – quizzes                                          (member 5)
  QuizzesController.php            BACK  – quizzes                                          (member 5)
  QuizQuestionsController.php      BACK  – quiz questions                                   (member 5)
  EventsController.php             FRONT – events + my registrations                        (member 6)
  EventsAdminController.php        BACK  – events                                           (member 6)
  RegistrationsController.php      BACK  – registrations                                    (member 6)
app/Models/
  Model.php                        all(), find(), where(), insert(), update(), delete()
  User.php                         admin + demo customer + sign-up accounts                 (shared)
  Ticket.php, TicketReply.php      ticket_reply.ticket_id -> ticket.id                      (member 1)
  Category.php, Product.php        product.category_id -> category.id                       (member 2)
  Cart.php, Order.php,             order_line.order_id -> order.id,
  OrderLine.php, Payment.php       payment.order_id -> order.id                             (member 3)
  Article.php, ArticleComment.php  article_comment.article_id -> article.id                 (member 4)
  Quiz.php, QuizQuestion.php       quiz_question.quiz_id -> quiz.id                         (member 5)
  Event.php, EventRegistration.php event_registration.event_id -> event.id                  (member 6)
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
3. Open http://localhost:8000/index.php (the home page opens by default).

*With XAMPP:* copy the folder into `htdocs`, start Apache, then open
`http://localhost/paraxis/index.php?page=home`.

## Usage

### Accounts (no database yet)

| Account  | Email                       | Password          | Goes to                        |
|----------|-----------------------------|-------------------|--------------------------------|
| Admin    | `admin@paraxis.demo`      | `Admin123!`       | `?page=orders` (back office)   |
| Customer | `customer@paraxis.demo`   | `Customer123!`    | `?page=shop`                   |
| Customer | create one with **Sign up** | 8 characters min. | `?page=shop` (or `?next=` page)|

### Test scenario
1. **Home** → **Explore products**. **Shop**: filter, search, click **+** or **Add to bag** (the page is not reloaded, the Bag counter updates). **Bag**: change quantities → log in → enter an
   address and pay (card `4242 4242 4242 4242` = success, `4000 0000 0000 0002` = declined, any future MM/YY, any CVV).
2. **My orders**: the order is `pending`; cancel it → the stock comes back.
3. **Health advice**: open an article, post a comment.
4. **Quiz**: pick a quiz → answer → **See my score**.
5. **Events**: filter, search, **Register**. **My registrations**: cancel a registration.
6. **Support**: open a ticket, add a reply.
7. **Log out**, log in as **admin** → every back-office page: Support tickets, Categories, Products, Orders,
   Articles, Comments, Quizzes, Quiz questions, Events, Registrations. The customer's order, comment,
   registration and ticket are all there.

> Payment is simulated: no bank, nothing is charged, card data is never stored.
> Data is stored in the PHP session (`tickets`, `ticket_replies`, `categories`, `products`, `cart`, `orders`,
> `order_lines`, `payments`, `articles`, `article_comments`, `quizzes`, `quiz_questions`, `events`,
> `event_registrations`). Use the same browser for the customer and the admin; closing the browser resets the data.

### Add a page (workshop step 11)
1. Create `app/Views/pages/<name>.php` with the page content only.
2. Create `app/Controllers/<Name>Controller.php` extending `Controller` (set `$page`, `$title`, `$layout`) and call `$this->render('<name>')`.
3. Point the route in `app/routes.php` to the new controller.

## Contributors

| Member   | Module                         |
|----------|--------------------------------|
| Member 1 – *[name]* | Customer support (tickets) |
| Member 2 – *[name]* | Catalog (categories, products) |
| Member 3 – *[name]* | Orders and payments        |
| Member 4 – Mariem Sammar | Health advice (articles, comments) |
| Member 5 – *[name]* | Health quiz                |
| Member 6 – *[name]* | Events & workshops         |

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
