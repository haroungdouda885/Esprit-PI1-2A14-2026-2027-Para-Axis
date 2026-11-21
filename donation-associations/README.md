# Par'Axis — Donations & associations module (Template integration workshop)

Module entities: **Association**, **Donation**, **DonationItem** (+ the shared **Category** with `type = 'association'`).
Links of the class diagram:

| Link | Multiplicity | Column |
|---|---|---|
| User **fait** Donation | 1 – 0..N | `donation.user_id` |
| Association **reçoit** Donation | 1 – 0..N | `donation.association_id` |
| Donation **contient** DonationItem | 1 – 0..N | `donation_item.donation_id` (ON DELETE CASCADE) |
| DonationItem **concerne** Product | 0..N – 1 | `donation_item.product_id` |
| Category **regroupe** Association | 1 – 0..N | `association.category_id` (category of type `association`) |

Same architecture as the team's other modules: **one entry point** (`index.php?page=...`)
for the front office AND the back office, routes `page => [Controller, method]`, two layouts,
page views without any `require_once`. No database yet.
The layouts, partials, includes, CSS and JS are the **same files as the other modules**; only the module pages change.

## Structure

```
index.php                          front controller + autoloader (index.php?page=...) + admin read-only rule
app/routes.php                     page key => [Controller, method]
app/helpers.php                    isActive(), groupIsActive(), e(), currentUser(), loginUser(), isAdmin(), fmtDate(), flash()
app/Controllers/
  Controller.php                   abstract base: render(), capture(), redirect(), isPost(), requireAdmin()
  DonationsController.php          FRONT  : index (associations), donate, mine (my donations), cancel
  AuthController.php               login, signup, logout
  AssociationsController.php       BACK   : index, save (create / edit), toggle (show / hide), delete
  DonationsAdminController.php     BACK   : index (filters), status, delete
  AssociationCategoriesController  BACK   : index, save (create / edit), delete
  PlaceholderController.php        empty pages for the other team members
app/Models/
  Model.php                        all(), find(), where(), insert(), update(), delete() — session storage
  Association.php                  starting rows + search(), forCategory(), totalReceived(), donationCount()
  Donation.php                     starting rows + statuses, forUser(), filter(), moveStock(), deleteWithItems()
  DonationItem.php                 starting rows + forDonation(), detailed(), usesProduct()
  Category.php                     shared with Catalog (member 2): types 'product' / 'association'
  Product.php                      copy of the Catalog products (member 2): read for product donations + stock
  User.php                         admin account + sign-up accounts
app/Views/
  front-layout.php                 front office: head, spinner, navbar, content, footer, scripts
  layout.php                       back office : head (+ preloader), sidebar, header, content, footer, scripts
  includes/                        PART 1: head.php, spinner.php, navbar.php, footer.php (+ back to top), scripts.php
  partials/                        PART 2: head.php, sidebar.php, header.php, footer.php, scripts.php
  pages/                           page content only: donations (+ donations-scripts), my-donations, login, signup,
                                   associations, donations-admin, association-categories, placeholder
  assets/css/front.css, admin.css, assets/js/main.js
```

## Run
- `php -S localhost:8000` in this folder, then open http://localhost:8000/index.php?page=donations
- or XAMPP: copy the folder into `htdocs`, start Apache, open
  http://localhost/paraxis-donations/index.php?page=donations

## Accounts (no database yet)

| Account | Email | Password | Goes to |
|---|---|---|---|
| Admin | `admin@parasante.demo` | `Admin123!` | `?page=donations-admin` (back office) |
| Customer | create one with **Sign up** | 8 characters min. | `?page=donations` |

Back-office pages call `requireAdmin()`: anyone else is sent to `?page=login&admin=1`.
The admin can **look** at the Donations page (yellow "read-only" bar) but cannot donate:
`donate`, `my-donations` and `donation-cancel` are refused for him in `index.php`, even if a form is sent by hand.

## Business rules
- Donate (logged-in customers): **money** (1 – 10 000 TND) or **products** (quantity ≤ stock; the value is computed
  from the product prices). Optional message, "stay anonymous" in the public list.
- A product donation **reserves the stock** at once; cancelling (customer: only while *pending*; admin: any status
  except *delivered*) puts the products back. Re-opening a cancelled donation takes them again (if there is stock).
- Status flow: `pending` → `confirmed` → `delivered` (or `cancelled`). "Received" totals count *confirmed* + *delivered*.
- An association that has received donations cannot be deleted (hide it instead). Its name must be unique.
- An association category used by associations cannot be deleted.

## Scenario to test
1. Sign up as a customer → **Donations**: filter by category, search → **Donate** money to one association,
   then products to another (watch the "in stock" numbers go down).
2. **My donations**: both are *pending*; cancel the product donation → the stock comes back.
3. Log out, log in as admin → **Donations**: filter by association / type / status, change a status
   (confirmed → delivered), try to cancel a delivered one (refused), delete one.
4. **Associations**: create one (choose its category), edit it, hide it (it disappears from the front), try to delete
   an association that has donations (refused).
5. **Association categories**: add "Education", use it for an association, try to delete it (refused).

Data is kept in the PHP session (`$_SESSION['associations']`, `['donations']`, `['donation_items']`,
`['categories']`, `['products']`), starting from the rows written in the models. Close the browser to reset.
Use the **same browser** for the customer and the admin.

## Workshop PDF steps

| PDF step | File(s) |
|---|---|
| Part 1 — Step 2: `includes/` | `app/Views/includes/` |
| Part 1 — Step 3–7: head, spinner, navbar (+ active link), footer (+ back to top), scripts | `includes/*.php` |
| Part 1 — Step 8/10: assemble the pages | `front-layout.php` + `pages/donations.php`, `my-donations.php`, `login.php`, `signup.php` |
| Part 2 — Step 2: `partials/` | `app/Views/partials/` |
| Part 2 — Step 3–7: head + preloader, sidebar (dynamic), header, footer, scripts + `$extraScripts` | `partials/*.php`, `pages/donations-scripts.php` |
| Part 2 — Step 8: layout + base Controller | `layout.php`, `Controllers/Controller.php` |
| Part 2 — Step 9: build a page | `pages/associations.php` + `AssociationsController.php` |
| Part 2 — Step 11: other pages | `pages/donations-admin.php`, `association-categories.php` + their controllers + lines in `routes.php` |

## Add a page
1. Create `app/Views/pages/<name>.php` with the page content only.
2. Create `app/Controllers/<Name>Controller.php` extending `Controller` (set `$page`, `$title`, `$layout`)
   and call `$this->render('<name>')`.
3. Add (or point) the route in `app/routes.php`.

## Integration in the team project
- `Product.php` and `Category.php` belong to the Catalog module (member 2): in the team project use theirs
  (Category gets the `type` column, the shared **Categories** page manages both types).
- In the team project the admin is read-only on the whole front office (`CUSTOMER_ACTIONS` in `index.php`).

## Next step (database)
Only `Models/Model.php` changes: `all()`, `find()`, `where()`, `insert()`, `update()`, `delete()`
will run PDO queries. Controllers and Views stay the same.
