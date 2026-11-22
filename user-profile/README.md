# Par'Axis — Users module: My account: profile, password, addresses, delete account (member 6)

The logged-in user manages his account: **edit the profile** (name, email, phone), **change the password**, **delivery addresses** (add, edit, choose the default one, delete) and **delete the account**.

This folder is **independent**: it contains only this part of the Users module + the shared base needed to run it
(database connection + automatic tables, `User` model, login / logout, CSRF protection, layouts).
The other parts of the module are not in this folder (no page, no link).

## My files

| File | Role |
|---|---|
| `app/Controllers/ProfileController.php` | index, update (profile), password, saveAddress, setDefaultAddress, deleteAddress, deleteAccount |
| `app/Views/pages/my-account.php` | the My account page (profile, password, addresses, danger zone) |
| `app/Models/Address.php` | table `addresses`: forUser, defaultFor, save, setDefault, delete |
| `app/Mailer.php` | emails: "confirm your new email" and "your password was changed" |
| `SignupController.php + verify-email.php` | from member 2, only to confirm a changed email |

Shared base (same in every part): `index.php`, `app/config.php`, `app/Database.php` + `app/Schema.php` (creates the tables), `app/helpers.php`, `app/Security.php`,
`app/Auth.php`, `app/Models/User.php`, `AuthToken.php`, `LoginAttempt.php`, `Controllers/Controller.php`, `LoginController.php`,
the layouts (`front-layout.php`, `layout.php`, `includes/`, `partials/`) and the CSS / JS.

## Database
Tables of this part: `users`, `auth_tokens`, `login_attempts`, `addresses`.

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
- XAMPP: copy the folder `paraxis-users-profile` into `htdocs`, open http://localhost/paraxis-users-profile/index.php
- or `php -S localhost:8000` in this folder, then open http://localhost:8000/index.php

The project opens directly on its own page. When you are logged in, the **Log in** page shows who you are (name, role, email confirmed) with a **Log out** button.

## Scenario to test
1. Log in as **customer** (`customer@paraxis.demo` / `Customer123!`) → **My account**.
2. **Profile**: change the name and the phone (Tunisian format, e.g. `22 123 456`) → saved. Try an email already used → refused.
3. Change the **email** → it must be confirmed again: a link is sent to the new address (configure the emails first, section **Emails**).
4. **Password**: wrong current password → refused; weak new password → refused; good one → changed + *"Your password was changed"* email.
5. **Addresses**: add 2 addresses, choose the default one, edit one, delete one (the default moves to another address).
6. **Delete my account**: wrong password → refused; right password → account deleted, logged out (its addresses are deleted too, ON DELETE CASCADE).
7. An admin account cannot be deleted from this page.

## Security used in this part
- `password_hash()` / `password_verify()`, prepared statements (PDO), tokens stored **hashed** (sha256).
- CSRF token in every POST form (`Security::csrfField()` + `Security::checkForm()`).
- `session_regenerate_id()` at login / logout, HttpOnly + SameSite session cookie.
