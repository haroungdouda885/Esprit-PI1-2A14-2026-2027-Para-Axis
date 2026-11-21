<?php

namespace App;

use App\Models\LoginAttempt;

/**
 * MEMBER 5 - Security toolbox used by the forms of the other members:
 *   CSRF token, CAPTCHA (built-in image or Google reCAPTCHA), brute force protection, password rules.
 *
 * In a controller:   if ($err = Security::checkForm()) { $message = $err; } else { ...process the form... }
 * In a view (form):  <?= \App\Security::csrfField() ?>   and, before the submit button,   <?= \App\Security::captchaWidget() ?>
 */
class Security
{
    // ---------- CSRF ----------
    public static function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /** Hidden input to put inside every POST form */
    public static function csrfField(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . e(self::csrfToken()) . '">';
    }

    public static function verifyCsrf(): bool
    {
        $sent = $_POST['csrf_token'] ?? '';
        return is_string($sent) && $sent !== '' && hash_equals(self::csrfToken(), $sent);
    }

    // ---------- CAPTCHA (built-in image, or Google reCAPTCHA) ----------
    private const CAPTCHA_CHARS = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // no 0/O, 1/I: easier to read

    /** 'builtin', 'recaptcha' or 'off' (recaptcha without keys falls back to builtin) */
    public static function captchaDriver(): string
    {
        $driver = (string) config('captcha.driver', 'builtin');
        if ($driver === 'recaptcha' && (empty(config('recaptcha.site_key')) || empty(config('recaptcha.secret_key')))) {
            return 'builtin';
        }
        return in_array($driver, ['builtin', 'recaptcha', 'off'], true) ? $driver : 'builtin';
    }

    public static function captchaEnabled(): bool
    {
        return self::captchaDriver() !== 'off';
    }

    /** The CAPTCHA block to put in a form, just before the submit button */
    public static function captchaWidget(): string
    {
        $driver = self::captchaDriver();
        if ($driver === 'off') {
            return '';
        }
        if ($driver === 'recaptcha') {
            return '<script src="https://www.google.com/recaptcha/api.js" async defer></script>'
                 . '<div class="g-recaptcha" data-sitekey="' . e(config('recaptcha.site_key')) . '"></div>';
        }
        $src = 'index.php?page=captcha&amp;t=' . bin2hex(random_bytes(4));
        return '<label for="captcha">Security check</label>'
             . '<div class="captcha-box">'
             . '<img class="captcha-img" src="' . $src . '" width="180" height="60" alt="Security code: type the letters you see">'
             . '<button type="button" class="captcha-refresh" data-captcha-refresh title="Show another image" aria-label="Show another image">&#x21bb;</button>'
             . '</div>'
             . '<input id="captcha" name="captcha" autocomplete="off" autocapitalize="characters" spellcheck="false" maxlength="10" placeholder="Type the letters of the image">';
    }

    /** Creates a new code, keeps it in the session and returns it (called by CaptchaController) */
    public static function newCaptchaCode(): string
    {
        $length = max(4, min(8, (int) config('captcha.length', 5)));
        $code   = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= self::CAPTCHA_CHARS[random_int(0, strlen(self::CAPTCHA_CHARS) - 1)];
        }
        $_SESSION['captcha'] = ['hash' => hash('sha256', $code), 'time' => time()];
        return $code;
    }

    public static function verifyCaptcha(): bool
    {
        $driver = self::captchaDriver();
        if ($driver === 'off') {
            return true;
        }
        if ($driver === 'builtin') {
            $saved = $_SESSION['captcha'] ?? null;
            unset($_SESSION['captcha']); // one try per image: a bot cannot guess again and again
            $typed = strtoupper(preg_replace('/\s+/', '', (string) ($_POST['captcha'] ?? '')));
            return is_array($saved) && $typed !== ''
                && time() - (int) $saved['time'] <= (int) config('captcha.ttl', 600)
                && hash_equals($saved['hash'], hash('sha256', $typed));
        }

        $response = $_POST['g-recaptcha-response'] ?? '';
        if (!is_string($response) || $response === '') {
            return false;
        }
        $context = stream_context_create(['http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query([
                'secret'   => config('recaptcha.secret_key'),
                'response' => $response,
                'remoteip' => self::clientIp(),
            ]),
            'timeout' => 5,
        ]]);
        $raw = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
        if ($raw === false) {
            return false;
        }
        $data = json_decode($raw, true);
        return !empty($data['success']);
    }

    /** CSRF + CAPTCHA in one call. Returns an error message, or null when everything is fine. */
    public static function checkForm(bool $captcha = true): ?string
    {
        if (!self::verifyCsrf()) {
            return 'Your session expired. Please reload the page and try again.';
        }
        if ($captcha && !self::verifyCaptcha()) {
            return self::captchaDriver() === 'builtin'
                ? 'The security code is not correct. Please type the letters of the new image.'
                : 'Please confirm that you are not a robot.';
        }
        return null;
    }

    // ---------- Brute force protection ----------
    public static function clientIp(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    public static function lockoutMinutes(): int
    {
        return (int) config('security.lockout_minutes', 15);
    }

    /** true when this email (or this IP address) failed to log in too many times recently */
    public static function isLockedOut(string $email): bool
    {
        $max     = (int) config('security.max_attempts', 5);
        $minutes = self::lockoutMinutes();
        $email   = strtolower(trim($email));
        return LoginAttempt::countByEmail($email, $minutes) >= $max
            || LoginAttempt::countByIp(self::clientIp(), $minutes) >= $max * 4; // one IP may be a whole school / office
    }

    public static function recordFailure(string $email): void
    {
        LoginAttempt::record(strtolower(trim($email)), self::clientIp());
    }

    public static function clearAttempts(string $email): void
    {
        LoginAttempt::clearForEmail(strtolower(trim($email)));
    }

    // ---------- Password rules (sign up, reset, change password) ----------
    public static function passwordError(string $password, string $confirm): ?string
    {
        if (strlen($password) < 8) {
            return 'The password must have at least 8 characters.';
        }
        if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
            return 'The password must contain at least one letter and one number.';
        }
        if ($password !== $confirm) {
            return 'The passwords do not match.';
        }
        return null;
    }
}
