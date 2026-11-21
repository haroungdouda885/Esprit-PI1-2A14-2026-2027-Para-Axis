# Par'Axis — Users module: Forgot / reset password 

A user who forgot his password asks for a **reset link** by email. The link is valid **60 minutes**, works **once** (token stored hashed in `password_resets`), and after the change a **"your password was changed"** email warns the owner.

This folder is **independent**: it contains only this part of the Users module + the shared base needed to run it
(database connection + automatic tables, `User` model, login / logout, CSRF protection, layouts).
The other parts of the module are not in this folder (no page, no link).

## My files

| File | Role |
|---|---|
| `app/Controllers/PasswordResetController.php` | forgot (ask for the link), reset (check the token, new password) |
| `app/Views/pages/forgot-password.php` | email form + "Check your inbox" screen |
| `app/Views/pages/reset-password.php` | new password form (or "link invalid / expired") |
| `app/Models/PasswordReset.php` | table `password_resets`: create, find valid token, mark used |
| `app/Mailer.php` | sends the emails by SMTP (Gmail…), no Composer; a copy of each email is saved in `storage/mails/` |

Shared base (same in every part): `index.php`, `app/config.php`, `app/Database.php` + `app/Schema.php` (creates the tables), `app/helpers.php`, `app/Security.php`,
`app/Auth.php`, `app/Models/User.php`, `AuthToken.php`, `LoginAttempt.php`, `Controllers/Controller.php`, `LoginController.php`,
the layouts (`front-layout.php`, `layout.php`, `includes/`, `partials/`) and the CSS / JS.

## Database
Tables of this part: `users`, `auth_tokens`, `login_attempts`, `password_resets`.

**Nothing to import.** Just start **MySQL** in XAMPP: at the first visit the site creates the database `paraxis`,
the tables of this part (`app/Schema.php`, `CREATE TABLE IF NOT EXISTS`) and 2 demo accounts.
The database login is in `app/config.php` (XAMPP default: user `root`, empty password).

| Account | Email | Password |
|---|---|---|
| Admin | `admin@paraxis.demo` | `Admin123!` |
| Customer | `customer@paraxis.demo` | `Customer123!` |

## Emails
1. Copy `app/config.local.example.php` to `app/config.local.php` (it is in `.gitignore`: never commit it).
2. Gmail: turn on 2-Step Verification, create an **App password** (myaccount.google.com/apppasswords) and put the
   address + the 16 letters in `mail.username` / `mail.password`.
3. `extension=openssl` must be enabled in `php.ini` (XAMPP: enabled by default). Restart Apache.
4. If an email cannot be sent, the page shows *We could not send the email* and the reason is in the PHP error log
   (`xampp/apache/logs/error.log`). A copy of every email is also saved in `storage/mails/`.

## Run
- XAMPP: copy the folder `paraxis-users-forgot-password` into `htdocs`, open http://localhost/paraxis-users-forgot-password/index.php
- or `php -S localhost:8000` in this folder, then open http://localhost:8000/index.php

The project opens directly on its own page. When you are logged in, the **Log in** page shows who you are (name, role, email confirmed) with a **Log out** button.

## Scenario to test
1. Configure the emails first (section **Emails** below). Put **your real email** on the demo customer: phpMyAdmin → database `paraxis` → table `users` → edit the email of `customer@paraxis.demo`.
2. **Forgot password** → type an email that does not exist → same answer (*If an account exists…*): the site never says who has an account.
3. Type your email → the email arrives → **Choose a new password** → enter a weak password (refused), then a good one → *Password changed*.
4. A second email arrives: **Your password was changed**.
5. Log in with the **new** password (the old one no longer works).
6. Click the same reset link again → *invalid or expired* (single use). A link older than 60 minutes is refused too.
7. "Remember me" devices are logged out after a reset (table `auth_tokens` emptied for this user).

## Security used in this part
- `password_hash()` / `password_verify()`, prepared statements (PDO), tokens stored **hashed** (sha256).
- CSRF token in every POST form (`Security::csrfField()` + `Security::checkForm()`).
- `session_regenerate_id()` at login / logout, HttpOnly + SameSite session cookie.
