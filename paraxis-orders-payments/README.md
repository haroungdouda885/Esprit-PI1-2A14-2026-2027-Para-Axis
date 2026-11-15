# Par'Axis – Online Parapharmacy 

## Overview

This project was developed as part of the **Web Technologies Project (PW) – 2nd Year Engineering Program** at
**Esprit School of Engineering** (Academic Year 2026–2027).

Par'Axis is an online parapharmacy web application. It brings together, in **one PHP MVC application**, a
front office for customers and a back office for administrators. This repository contains the integration of
two team modules built during the *Template Integration* workshop:

- **Catalog** (member 2): categories and products, shop with filters and search, product page.
- **Orders and payments** (member 3): shopping bag, simulated checkout, orders, payments and stock updates.

Problem solved: customers browse the catalog, fill a bag and place an order in the same store, and admins manage
the catalog and the orders from a single dashboard. The two modules share the same `Product` and `Category`
entities: the stock set in the catalog is the stock checked and updated by the orders. The other modules
(support, health advice, quiz, events) are already routed as empty pages and will be integrated the same way.

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
- Bag (orders): "+" on each card or **Add to bag** on the product page, change quantities (limited by the stock).
- Checkout: delivery address + payment method (simulated card, cash on delivery, bank transfer).
- My orders: order history, cancel a *pending* / *processing* order (stock comes back, payment refunded / failed).
- Sign up / log in / log out (sign-up always creates a *customer* account), navbar with the active link highlighted.

**Back office (admins only)**
- Categories: add, delete (its products are deleted too).
- Products: add, change the stock, delete, stock status (in stock / low stock / out of stock).
- Orders: filter by status, change the order status (cancelled ⇄ other status moves the stock), change the
  payment status, delete an order (its lines and payment are deleted too).
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

One entry point for the front office **and** the back office: `index.php?page=<key>`.

```
index.php                          front controller + autoloader
app/routes.php                     page key => [Controller, method]
app/helpers.php                    isActive(), groupIsActive(), e(), currentUser(), loginUser(),
                                   isAdmin(), money(), flash(), takeFlash()
app/Controllers/
  Controller.php                   base: render(), capture(), redirect(), isPost(), requireAdmin(), blockAdmin()
  AuthController.php               login, signup, logout                                   (shared)
  ShopController.php               FRONT – shop + product page (member 2), add to bag (member 3)
  CategoriesController.php         BACK  – categories                                      (member 2)
  ProductsController.php           BACK  – products + stock                                (member 2)
  BagController.php                FRONT – bag + checkout                                  (member 3)
  MyOrdersController.php           FRONT – my orders + cancel                              (member 3)
  OrdersController.php             BACK  – orders + payments                               (member 3)
  PlaceholderController.php        empty pages for the other members' modules
app/Models/
  Model.php                        all(), find(), where(), insert(), update(), delete()
  User.php                         admin + demo customer + sign-up accounts
  Category.php, Product.php        product.category_id -> category.id                      (member 2)
  Order.php, OrderLine.php,        order_line.order_id -> order.id,
  Payment.php                      payment.order_id -> order.id                            (member 3)
  Cart.php                         shopping bag in $_SESSION['cart'] (not a table)         (member 3)
app/Views/
  front-layout.php                 head, spinner, navbar, content, footer, scripts
  layout.php                       head (+ preloader), sidebar, header, content, footer, scripts
  includes/                        front office partials (Part 1 of the workshop)
  partials/                        back office partials  (Part 2 of the workshop)
  pages/                           page content only (no require_once)
  assets/css, assets/js
```

**Link between the two modules:** `Product` is written by the Catalog (create, stock, delete) and read by the
Orders (bag, checkout). Placing an order calls `Product::changeStock(-qty)`, cancelling it `changeStock(+qty)`.
Order lines keep a snapshot of the product name and price, so deleting a product never breaks an old order;
a deleted product is removed from the bag automatically.

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
3. Open http://localhost:8000/index.php?page=shop

*With XAMPP:* copy the folder into `htdocs`, start Apache, then open
`http://localhost/<folder>/index.php?page=shop`.

## Usage

### Accounts (no database yet)

| Account  | Email                     | Password          | Goes to                         |
|----------|---------------------------|-------------------|---------------------------------|
| Admin    | `admin@parasante.demo`    | `Admin123!`       | `?page=orders` (back office)    |
| Customer | `customer@parasante.demo` | `Customer123!`    | `?page=shop`                    |
| Customer | create one with **Sign up** | 8 characters min. | `?page=shop`                  |

### Test scenario
1. **Shop**: click a category filter, search, open a product page → **Add to bag**.
2. **Bag**: change quantities → log in as the customer → enter an address and pay
   (card `4242 4242 4242 4242` = success, `4000 0000 0000 0002` = declined, any future MM/YY, any CVV).
3. **My orders**: the order is `pending`; open the product again → the stock went down. Cancel the order → it comes back.
4. **Log out**, log in as **admin** → **Products**: set a stock to 0 → the Shop shows "Out of stock"; add a product.
5. **Categories**: add one (new filter on the Shop), delete one (its products are deleted too).
6. **Orders**: filter by status, change the order and payment status, delete an order.

> Payment is simulated: no bank, nothing is charged, card data is never stored.
> Data is stored in the PHP session (`categories`, `products`, `cart`, `orders`, `order_lines`, `payments`).
> Use the same browser for the customer and the admin; closing the browser resets the data.

### Add a page (workshop step 11)
1. Create `app/Views/pages/<name>.php` with the page content only.
2. Create `app/Controllers/<Name>Controller.php` extending `Controller` (set `$page`, `$title`, `$layout`) and call `$this->render('<name>')`.
3. Point the route in `app/routes.php` to the new controller.

## Contributors

| Member   | Module                         |
|----------|--------------------------------|
| Member 1 – *[name]* | Customer support (tickets) |
| Member 2 – Adem Debbich | Catalog (categories, products) |
| Member 3 – Firas Zouinkhi | Sales (orders, payments)   |
| Member 4 – *[name]* | Health advice (articles, comments) |
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
