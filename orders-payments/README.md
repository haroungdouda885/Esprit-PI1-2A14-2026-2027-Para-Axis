# Par'Axis — Orders and payments module (Template integration workshop)

Module entities: **Order** + **OrderLine** + **Payment**
(`order_line.order_id` → `order.id`, `payment.order_id` → `order.id`).
Same architecture as the team's *customer-support* and *health-advice* modules: **one entry point**
(`index.php?page=...`) for the front office AND the back office, routes `page => [Controller, method]`,
two layouts, page views without any `require_once`. No database yet.
The layouts, partials, includes, CSS and JS are the **same files as the other modules**; only the module pages change.

## Structure

```
index.php                          front controller + autoloader (index.php?page=...)
app/routes.php                     page key => [Controller, method]
app/helpers.php                    isActive(), e(), currentUser(), loginUser() + isAdmin(), money(), flash()
app/Controllers/
  Controller.php                   abstract base: render(), capture(), redirect(), isPost(), requireAdmin(), blockAdmin()
  ShopController.php               FRONT : index (product list), add (add to bag), demo (reload demo bag)
  BagController.php                FRONT : index (bag + checkout form), update (+ / −), checkout (order + lines + payment)
  MyOrdersController.php           FRONT : index (my orders), cancel (pending / processing orders only)
  AuthController.php               login, signup, logout
  OrdersController.php             BACK  : index (filter by status), status, payment, delete
  PlaceholderController.php        empty pages for the other team members
app/Models/
  Model.php                        all(), find(), where(), insert(), update(), delete() — session storage
  Order.php, OrderLine.php, Payment.php   module entities
  Product.php, Category.php        read from the Catalog module (stock is updated by the orders)
  Cart.php                         shopping bag in $_SESSION['cart'] (not a table)
  User.php                         admin + demo customer + sign-up accounts
app/Views/
  front-layout.php / layout.php    front office / back office layouts
  includes/                        PART 1: head, spinner, navbar, footer (+ back to top), scripts
  partials/                        PART 2: head (+ preloader), sidebar, header, footer, scripts
  pages/                           shop, bag (+ bag-scripts), my-orders, login, signup, orders, placeholder
  assets/css/front.css, admin.css, assets/js/main.js
```

## Run
- `php -S localhost:8000` in this folder, then open http://localhost:8000/index.php?page=shop
- or XAMPP: copy the folder into `htdocs`, start Apache, open
  http://localhost/parasante-orders-payments/index.php?page=shop

## Accounts (no database yet)

| Account | Email | Password | Goes to |
|---|---|---|---|
| Admin | `admin@parasante.demo` | `Admin123!` | `?page=orders` (back office) |
| Customer | `customer@parasante.demo` | `Customer123!` | `?page=shop` |
| Customer | create one with **Sign up** | 8 characters min. | `?page=shop` |

- Back-office pages call `requireAdmin()`: anyone else is sent to `?page=login&admin=1`.
- **The admin has no shop window**: Shop, Bag and My orders are hidden in the navbar, and these pages
  call `blockAdmin()` (the admin is sent to `?page=orders`). To buy, log out and log in as a customer.

## Scenario to test
1. A sample bag is loaded on the first visit (2 × first product, 1 × the next two). **Reload demo bag** restores it.
2. Open **Bag** → change quantities (limited by the stock) → log in → enter an address and pay.
3. **My orders**: the order is `pending`. **Cancel order** (pending / processing only): the stock comes back,
   a `paid` payment becomes `refunded`, an unpaid one becomes `failed`.
4. Log in as admin → **Orders**: filter by status, change the order status (cancelled ⇄ other status moves the stock),
   change the payment status (`paid` sets `paid_at`), delete an order (its payment and lines are deleted too).

## Payment testing (simulated — no bank, nothing is charged, card data is never stored)
- Success: `4242 4242 4242 4242` · Decline: `4000 0000 0000 0002`
- Expiry: any future MM/YY · CVV: any 3 (or 4) digits
- Card = payment `paid`; cash on delivery / bank transfer = payment `pending`.

Data is kept in the PHP session (`$_SESSION['orders']`, `['order_lines']`, `['payments']`, `['products']`, `['cart']`),
starting from the rows written in the models. Close the browser to reset.

## Next step (database)
Only `Models/Model.php` changes: `all()`, `find()`, `where()`, `insert()`, `update()`, `delete()`
will run PDO queries. Controllers and Views stay the same.
