# Par'Axis — Health advice module (Template integration workshop)

Module entities: **Article** + **ArticleComment** (`article_comment.article_id` → `article.id`).
Same architecture as the team's *customer-support* module: **one entry point** (`index.php?page=...`)
for the front office AND the back office, routes `page => [Controller, method]`, two layouts,
page views without any `require_once`. No database yet.
The layouts, partials, includes, CSS and JS are the **same files as customer-support**; only the module pages change.

## Structure

```
index.php                          front controller + autoloader (index.php?page=...)
app/routes.php                     page key => [Controller, method]
app/helpers.php                    isActive(), groupIsActive(), e(), currentUser(), loginUser()
app/Controllers/
  Controller.php                   abstract base: render(), capture(), redirect(), isPost(), requireAdmin()
  HealthAdviceController.php       FRONT  : index (list), show (article + comments), comment (post a comment)
  AuthController.php               login, signup, logout
  ArticlesController.php           BACK   : index, create, status (published/draft), delete
  CommentsController.php           BACK   : index, delete
  PlaceholderController.php        empty pages for the other team members
app/Models/
  Model.php                        all(), find(), where(), insert(), update(), delete() — session storage
  Article.php, ArticleComment.php  starting rows + published(), forArticle(), deleteWithComments()
  User.php                         admin account + sign-up accounts
app/Views/
  front-layout.php                 front office: head, spinner, navbar, content, footer, scripts
  layout.php                       back office : head (+ preloader), sidebar, header, content, footer, scripts
  includes/                        PART 1: head.php, spinner.php, navbar.php, footer.php (+ back to top), scripts.php
  partials/                        PART 2: head.php, sidebar.php, header.php, footer.php, scripts.php
  pages/                           page content only: health-advice, article, login, signup,
                                   articles, comments, placeholder
  assets/css/front.css, admin.css, assets/js/main.js
```

## Run
- `php -S localhost:8000` in this folder, then open http://localhost:8000/index.php?page=health-advice
- or XAMPP: copy the folder into `htdocs`, start Apache, open
  http://localhost/paraxis-health-advice/index.php?page=health-advice

## Accounts (no database yet)

| Account | Email | Password | Goes to |
|---|---|---|---|
| Admin | `admin@parasante.demo` | `Admin123!` | `?page=articles` (back office) |
| Customer | create one with **Sign up** | 8 characters min. | `?page=health-advice` |

Back-office pages call `requireAdmin()`: anyone else is sent to `?page=login&admin=1`.

## Scenario to test
1. Sign up as a customer → open an article → post a comment: it is visible right away.
2. Log out, log in as admin → **Comments**: the new comment is in the list → **Delete** removes it.
3. Back on the article page: the comment is gone.
4. **Articles**: add an article, switch Published/Draft (drafts disappear from the front), delete one
   (its comments are deleted too).

Data is kept in the PHP session (`$_SESSION['articles']`, `$_SESSION['article_comments']`), starting
from the rows written in the models. Close the browser to reset.

## Workshop PDF steps

| PDF step | File(s) |
|---|---|
| Part 1 — Step 2: `includes/` | `app/Views/includes/` |
| Part 1 — Step 3–7: head, spinner, navbar (+ active link), footer (+ back to top), scripts | `includes/*.php` |
| Part 1 — Step 8/10: assemble the pages | `front-layout.php` + `pages/health-advice.php`, `article.php`, `login.php`, `signup.php` |
| Part 2 — Step 2: `partials/` | `app/Views/partials/` |
| Part 2 — Step 3–7: head + preloader, sidebar (dynamic), header, footer, scripts + `$extraScripts` | `partials/*.php` |
| Part 2 — Step 8: layout + base Controller | `layout.php`, `Controllers/Controller.php` |
| Part 2 — Step 9: build a page | `pages/articles.php` + `ArticlesController.php` |
| Part 2 — Step 11: other pages | `pages/comments.php` + `CommentsController.php` + lines in `routes.php` |

## Add a page
1. Create `app/Views/pages/<name>.php` with the page content only.
2. Create `app/Controllers/<Name>Controller.php` extending `Controller` (set `$page`, `$title`, `$layout`)
   and call `$this->render('<name>')`.
3. Add (or point) the route in `app/routes.php`.

## Next step (database)
Only `Models/Model.php` changes: `all()`, `find()`, `where()`, `insert()`, `update()`, `delete()`
will run PDO queries. Controllers and Views stay the same.
