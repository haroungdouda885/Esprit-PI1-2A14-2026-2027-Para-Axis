# Para'Axis Customer Support — Workshop "Template Integration"



```
index.php                     front controller + autoloader (index.php?page=...)
app/routes.php                page key => [Controller, method]
app/Controllers/Controller.php    abstract base: render(), capture(), redirect()
app/Controllers/SupportController.php / AdminController.php / AuthController.php
app/Controllers/PlaceholderController.php   empty pages for the other team members
app/Models/Ticket.php, TicketReply.php      session storage (replace with PDO later)
app/Views/layout.php          back-office layout: head, sidebar, header, content, footer, scripts
app/Views/front-layout.php    front-office layout: head, spinner, navbar, content, footer, scripts
app/Views/partials/           back office  : head.php (head + preloader), sidebar.php, header.php, footer.php, scripts.php
app/Views/includes/           front office : head.php, spinner.php, navbar.php, footer.php (+ back to top), scripts.php
app/Views/pages/              page content only (no require_once)
app/Views/assets/css|js       front.css, admin.css, main.js
```

## Run
- `php -S localhost:8000` in this folder, then open http://localhost:8000/index.php?page=support
- or XAMPP: copy the folder to `htdocs`, open http://localhost/customer-support/index.php?page=support

Log in as **Customer** to create tickets, or **Support admin** to answer them (index.php?page=admin).

## Add a page 
1. Create `app/Views/pages/<name>.php` with the page content only.
2. Create `app/Controllers/<Name>Controller.php` extending `Controller` (set `$page`, `$title`, `$layout`) and call `$this->render('<name>')`.
3. Point the route in `app/routes.php` to the new controller.
