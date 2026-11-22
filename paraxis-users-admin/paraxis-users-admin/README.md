# Par'Axis — Users module: Back office: user management (member 1)

The admin manages all the accounts from the back office: list with statistics, search and filters, **create** a user (customer or admin), **block / unblock**, **change the role**, **confirm the email**, **delete**.

This folder is **independent**: it contains only this part of the Users module + the shared base needed to run it
(database connection + automatic tables, `User` model, login / logout, CSRF protection, layouts).
The other parts of the module are not in this folder (no page, no link).

## My files

| File | Role |
|---|---|
| `app/Controllers/UsersAdminController.php` | index (list, search, filters, stats), create, block, role, verify, delete |
| `app/Views/pages/users-admin.php` | back-office page: stats, "New user" form, users table with actions |
| `app/Views/assets/css/users-admin.css` | styles of the page |
| `app/Models/User.php (end of file)` | admin methods: search, counts, createWithRole, setBlocked, setRole, markVerified, countAdmins |

Shared base (same in every part): `index.php`, `app/config.php`, `app/Database.php`, `app/helpers.php`, `app/Security.php`,
`app/Auth.php`, `app/Models/User.php`, `AuthToken.php`, `LoginAttempt.php`, `Controllers/Controller.php`, `LoginController.php`,
the layouts (`front-layout.php`, `layout.php`, `includes/`, `partials/`) and the CSS / JS.

## Database
Tables of this part: `users`, `auth_tokens`, `login_attempts`.

**The site does not create the database or the tables.** Create them yourself in phpMyAdmin (SQL tab):
the full script is in `paraxis.sql` (database `paraxis`, the 3 tables and 2 demo accounts).
The database login is in `app/config.php` (XAMPP default: user `root`, empty password).

| Account | Email | Password |
|---|---|---|
| Admin | `admin@paraxis.demo` | `Admin123!` |
| Customer | `customer@paraxis.demo` | `Customer123!` |

## Run
- XAMPP: copy the folder `paraxis-users-admin` into `htdocs`, open http://localhost/paraxis-users-admin/index.php
- or `php -S localhost:8000` in this folder, then open http://localhost:8000/index.php

The project opens directly on its own page. When you are logged in, the **Log in** page shows who you are (name, role, email confirmed) with a **Log out** button.

## Scenario to test
1. Log in as **admin** (`admin@paraxis.demo` / `Admin123!`) → you arrive on **Users**.
2. **New user**: create a customer (and try a duplicate email or a weak password → refused).
3. **Search** by name / email, filter by role and status.
4. **Block** the customer → log out, try to log in with it → *This account is blocked*. Log in as admin again → **Unblock**.
5. **Change role** customer ↔ admin, **Confirm email** of an unconfirmed user.
6. **Delete** a user (his "remember me" tokens are deleted too, ON DELETE CASCADE).
7. Safety rules: you cannot block, delete or change the role of **your own** account, and the **last admin** cannot be removed.
8. A customer who opens `?page=users-admin` is sent to the login page.

## Security used in this part
- `password_hash()` / `password_verify()`, prepared statements (PDO), tokens stored **hashed** (sha256).
- CSRF token in every POST form (`Security::csrfField()` + `Security::checkForm()`).
- `session_regenerate_id()` at login / logout, HttpOnly + SameSite session cookie.
