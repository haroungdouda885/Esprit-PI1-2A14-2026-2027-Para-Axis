# Par'Axis — Catalog module (Template integration workshop)

Module entities: **Category** + **Product** (`product.category_id` → `category.id`).
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
  ShopController.php               FRONT  : index (products + category filter + search), show (one product)
  AuthController.php               login, signup, logout
  CategoriesController.php         BACK   : index, create, delete
  ProductsController.php           BACK   : index, create, stock (change the stock), delete
  PlaceholderController.php        empty pages for the other team members
app/Models/
  Model.php                        all(), find(), where(), insert(), update(), delete() — session storage
  Category.php, Product.php        starting rows + forCategory(), search(), stockStatus(), deleteWithProducts()
  User.php                         admin account + sign-up accounts
app/Views/
  front-layout.php                 front office: head, spinner, navbar, content, footer, scripts
  layout.php                       back office : head (+ preloader), sidebar, header, content, footer, scripts
  includes/                        PART 1: head.php, spinner.php, navbar.php, footer.php (+ back to top), scripts.php
  partials/                        PART 2: head.php, sidebar.php, header.php, footer.php, scripts.php
  pages/                           page content only: shop, product, login, signup,
                                   categories, products, placeholder
  assets/css/front.css, admin.css, assets/js/main.js
```

## Run
- `php -S localhost:8000` in this folder, then open http://localhost:8000/index.php?page=shop
- or XAMPP: copy the folder into `htdocs`, start Apache, open
  http://localhost/paraxis-catalog/index.php?page=shop

## Accounts (no database yet)

| Account | Email | Password | Goes to |
|---|---|---|---|
| Admin | `admin@parasante.demo` | `Admin123!` | `?page=products` (back office) |
| Customer | create one with **Sign up** | 8 characters min. | `?page=shop` |

Back-office pages call `requireAdmin()`: anyone else is sent to `?page=login&admin=1`.

## Scenario to test
1. Open **Shop** → click a category filter, type in the search box → open a product (price, stock status).
2. Log in as admin → **Products**: add a product → back on the Shop, it is in the grid.
3. **Products**: change a stock to 0 → the Shop shows "Out of stock"; delete a product.
4. **Categories**: add a category (new filter on the Shop), delete one (its products are deleted too).

The bag / checkout / orders belong to the Sales module (member 3): the **Bag** link is an empty page for now.

Data is kept in the PHP session (`$_SESSION['categories']`, `$_SESSION['products']`), starting
from the rows written in the models. Close the browser to reset.

## Workshop PDF steps

| PDF step | File(s) |
|---|---|
| Part 1 — Step 2: `includes/` | `app/Views/includes/` |
| Part 1 — Step 3–7: head, spinner, navbar (+ active link), footer (+ back to top), scripts | `includes/*.php` |
| Part 1 — Step 8/10: assemble the pages | `front-layout.php` + `pages/shop.php`, `product.php`, `login.php`, `signup.php` |
| Part 2 — Step 2: `partials/` | `app/Views/partials/` |
| Part 2 — Step 3–7: head + preloader, sidebar (dynamic), header, footer, scripts + `$extraScripts` | `partials/*.php` |
| Part 2 — Step 8: layout + base Controller | `layout.php`, `Controllers/Controller.php` |
| Part 2 — Step 9: build a page | `pages/products.php` + `ProductsController.php` |
| Part 2 — Step 11: other pages | `pages/categories.php` + `CategoriesController.php` + lines in `routes.php` |

## Add a page
1. Create `app/Views/pages/<name>.php` with the page content only.
2. Create `app/Controllers/<Name>Controller.php` extending `Controller` (set `$page`, `$title`, `$layout`)
   and call `$this->render('<name>')`.
3. Add (or point) the route in `app/routes.php`.

## Next step (database)
Only `Models/Model.php` changes: `all()`, `find()`, `where()`, `insert()`, `update()`, `delete()`
will run PDO queries. Controllers and Views stay the same.
