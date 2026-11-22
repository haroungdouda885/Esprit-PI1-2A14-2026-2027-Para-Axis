# Par'Axis — Users module: Sign up + email verification (member 2)

A visitor creates a **customer** account; a **verification link** (single use, stored hashed in `users.verify_token`) is sent by email. Clicking it sets `users.is_verified = 1`. A *Send the email again* button exists if the email got lost.

This folder is **independent**: it contains only this part of the Users module + the shared base needed to run it
(database connection + automatic tables, `User` model, login / logout, CSRF protection, layouts).
The other parts of the module are not in this folder (no page, no link).

## My files

| File | Role |
|---|---|
| `app/Controllers/SignupController.php` | signup (form + checks + email), verify (link from the email), resend |
| `app/Views/pages/signup.php` | sign-up form + "Check your inbox" screen |
| `app/Views/pages/verify-email.php` | "Email confirmed" / "Please confirm your email" + resend button |
| `app/Mailer.php` | sends the emails by SMTP (Gmail…), no Composer; a copy of each email is saved in `storage/mails/` |

Shared base (same in every part): `index.php`, `app/config.php`, `app/Database.php` + `app/Schema.php` (creates the tables), `app/helpers.php`, `app/Security.php`,
`app/Auth.php`, `app/Models/User.php`, `AuthToken.php`, `LoginAttempt.php`, `Controllers/Controller.php`, `LoginController.php`,
the layouts (`front-layout.php`, `layout.php`, `includes/`, `partials/`) and the CSS / JS.

## Database
Tables of this part: `users`, `auth_tokens`, `login_attempts`.

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
- XAMPP: copy the folder `paraxis-users-signup` into `htdocs`, open http://localhost/paraxis-users-signup/index.php
- or `php -S localhost:8000` in this folder, then open http://localhost:8000/index.php

The project opens directly on its own page. When you are logged in, the **Log in** page shows who you are (name, role, email confirmed) with a **Log out** button.

## Scenario to test
1. Configure the emails first (section **Emails** below).
2. **Sign up** with wrong data: short name, invalid email, weak password, passwords that do not match, an email that already exists → each one is refused with a clear message.
3. Sign up with good data → *Check your inbox*. You are logged in; **Confirm my email** shows that the email is not confirmed yet.
4. Open the email → **Confirm my email** → *Email confirmed*. **Log in** now shows **Email confirmed: ✅ yes**.
5. Click the same link again → refused (single use).
6. Sign up another account → **Confirm my email** in the menu → **Send the email again** → a new link arrives (the old one stops working).
7. phpMyAdmin → table `users`: `is_verified` goes from 0 to 1 and `verify_token` becomes NULL.

## Security used in this part
- `password_hash()` / `password_verify()`, prepared statements (PDO), tokens stored **hashed** (sha256).
- CSRF token in every POST form (`Security::csrfField()` + `Security::checkForm()`).
- `session_regenerate_id()` at login / logout, HttpOnly + SameSite session cookie.
