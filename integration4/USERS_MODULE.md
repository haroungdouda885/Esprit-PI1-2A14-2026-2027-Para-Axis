# Users module - who does what

Each member owns their own files. Do not edit another member's files: ask for a method name instead.

| # | Member work | Files (own them) | Table | Routes |
|---|-------------|------------------|-------|--------|
| 1 | **Back office: user management** (list, search, filters, create user, block / unblock, change role, confirm email, delete) | `Controllers/UsersAdminController.php`, `Views/pages/users-admin.php`, `Views/assets/css/users-admin.css`, admin methods at the end of `Models/User.php` (`search`, `counts`, `createWithRole`, `setBlocked`, `setRole`, `markVerified`, `countAdmins`) | `users` (uses `role`, `is_blocked`, `is_verified`) | `users-admin`, `user-create`, `user-block`, `user-role`, `user-verify`, `user-delete` |
| 2 | Sign up + email verification | `Controllers/SignupController.php`, `Views/pages/signup.php`, `Views/pages/verify-email.php`, `app/Mailer.php` | columns `is_verified`, `verify_token` in `users` | `signup`, `verify-email`, `resend-verification` |
| 3 | Login, logout, sessions, remember me | `Controllers/LoginController.php`, `Views/pages/login.php`, `app/Auth.php`, `Models/AuthToken.php`, `loginUser()/logoutUser()` in `helpers.php` | `auth_tokens` | `login`, `logout` |
| 4 | Forgot / reset password | `Controllers/PasswordResetController.php`, `Views/pages/forgot-password.php`, `Views/pages/reset-password.php`, `Models/PasswordReset.php` | `password_resets` | `forgot-password`, `reset-password` |
| 5 | CAPTCHA + security | `app/Security.php`, `Controllers/CaptchaController.php`, `Models/LoginAttempt.php`, session hardening in `index.php` | `login_attempts` | `captcha` (image) + functions used by 2, 3, 4, 6 |
| 6 | Profile + addresses + delete account | `Controllers/ProfileController.php`, `Views/pages/my-account.php`, `Models/Address.php`, `Views/assets/css/users.css` | `addresses` | `my-account`, `profile-update`, `change-password`, `address-save`, `address-default`, `address-delete`, `account-delete` |

Shared base, already written (10 minutes of setup, not a member's task): `app/Database.php`, the basic methods of `app/Models/User.php`, `app/config.php`, `database/*.sql`.

Shared (small edits, already done): `app/routes.php`, `app/helpers.php`, `app/Views/includes/navbar.php`, `app/Views/includes/head.php`, `app/Views/partials/head.php` and `partials/sidebar.php` (back office).
`AuthController.php` was removed: its job is now split between members 2 and 3.

## Setup (XAMPP)
1. Copy the `paraxis` folder into `C:\xampp\htdocs\`. Start **Apache** and **MySQL**.
2. phpMyAdmin (`http://localhost/phpmyadmin`) > **Import** > choose `database/ALL_users_module.sql`.
   (If you had created tables from an older SQL, drop the `paraxis` database first, `auth_tokens` changed.)
3. Check `app/config.php` (database user/password, and `app.url` = `http://localhost/paraxis`).
   Copy `app/config.local.example.php` to `app/config.local.php` and put the team Gmail address + **App password**
   (SMTP) there: verification and reset emails are then really sent. This file is never committed.
4. Open `http://localhost/paraxis/index.php`.
   Demo accounts: `admin@paraxis.demo / Admin123!` and `customer@paraxis.demo / Customer123!`.

## How the pieces talk to each other
- Forms (members 2, 3, 4, 6): put `<?= \App\Security::csrfField() ?>` in every POST form and call `Security::checkForm()` in the controller. Add `<?= \App\Security::captchaWidget() ?>` for CAPTCHA.
- CAPTCHA: built-in image by default (route `captcha`, `Controllers/CaptchaController.php`), checked by `Security::checkForm()`. Switch to Google reCAPTCHA with `captcha.driver = 'recaptcha'` + keys.
- Emails: sent by SMTP (`app/Mailer.php`, no Composer needed). Every email is also saved in `storage/mails/`; if sending fails the reason is in the PHP error log. `app.dev_show_links = true` also shows the links on screen (debug only).
- Who is logged in: `currentUser()` (array with id, name, email, phone, role, is_verified). Never read `$_SESSION` directly.
- Orders module: use `isVerified()` to refuse checkout to unconfirmed emails, and `Address::forUser($id)` / `Address::defaultFor($id)` for delivery addresses.
- Use `users.id` as the `user_id` foreign key in your own tables.

## Test checklist
- Sign up (valid and invalid data, duplicate email) > email file in `storage/mails/` > click the link > "Email confirmed".
- Login: wrong password 5 times > locked 15 min; admin goes to back office, customer to shop; "Remember me" survives closing the browser.
- Forgot password > email received > link > new password > "password changed" email received > old link no longer works.
- Login with a wrong CAPTCHA code > refused, a new image is shown; the ↻ button changes the image.
- Back office (log in as admin): Users page > create a user, search, block (that user can no longer log in), change role, confirm email, delete. You cannot block, delete or change your own account, and the last admin cannot be removed.
- My account: edit profile, change password, add / edit / delete addresses, delete account.
