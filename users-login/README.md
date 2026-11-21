# Par'Axis — Users module: Login, logout, sessions, remember me (member 3)

A user logs in with his email and password: **customers** go back to the page they came from, **admins** go to the **back office**. The session is protected (new session id at every login, HttpOnly + SameSite cookie). **Remember me** keeps the user logged in for 30 days with a secure cookie (selector + hashed token in `auth_tokens`). **Log out** ends the session and deletes the remember-me token.

This folder is **independent**: it contains only this part of the Users module + the shared base needed to run it
(database connection + automatic tables, `User` model, login / logout, CSRF protection, layouts).
The other parts of the module are not in this folder (no page, no link).

## My files

| File | Role |
|---|---|
| `app/Controllers/LoginController.php` | login (form, checks, blocked account, redirect), logout |
| `app/Views/pages/login.php` | login form (email, password, Remember me, links) |
| `app/Auth.php` | Remember me: create the cookie, restore the session from it, forget it at logout |
| `app/Models/AuthToken.php` | table `auth_tokens`: create (selector + sha256 of the token, 30 days), find, delete |
| `app/helpers.php` | `currentUser()`, `loginUser()` (session_regenerate_id + user saved without the password), `logoutUser()` |
| `app/Controllers/Controller.php` | `requireAdmin()`: the back office is only for admins |

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

## Run
- XAMPP: copy the folder `paraxis-users-login` into `htdocs`, open http://localhost/paraxis-users-login/index.php
- or `php -S localhost:8000` in this folder, then open http://localhost:8000/index.php

The project opens directly on its own page. When you are logged in, the **Log in** page shows who you are (name, role, email confirmed) with a **Log out** button.

## Scenario to test
1. **Log in** with an empty field, a wrong email, a wrong password → *Incorrect email or password* (the site never says which one is wrong).
2. Log in as **customer** (`customer@paraxis.demo` / `Customer123!`) → the **Log in** page now shows *You are logged in* with your name, email and role.
3. **Log out** → the login form is shown again.
4. A `next` parameter pointing outside the site (e.g. `?page=login&next=http://evil.com`) is ignored (only pages of the site are accepted).
5. Log in as **admin** (`admin@paraxis.demo` / `Admin123!`) → the page shows the role **admin** (in the team project admins are sent to the back office).
6. **Remember me**: log in with the box ticked → close the browser completely (or delete the `PHPSESSID` cookie) → open the site again: you are still logged in (cookie `paraxis_remember`, row in `auth_tokens`).
7. Log out → the `auth_tokens` row and the cookie are deleted: closing the browser now logs you out.
8. Blocked account: in phpMyAdmin set `is_blocked = 1` on the customer → login refused with *This account has been blocked*.
9. Session: the session id changes at every login (browser tools → Cookies → `PHPSESSID`), the cookie is HttpOnly + SameSite=Lax.

## Security used in this part
- `password_hash()` / `password_verify()`, prepared statements (PDO), tokens stored **hashed** (sha256).
- CSRF token in every POST form (`Security::csrfField()` + `Security::checkForm()`).
- `session_regenerate_id()` at login / logout, HttpOnly + SameSite session cookie.
