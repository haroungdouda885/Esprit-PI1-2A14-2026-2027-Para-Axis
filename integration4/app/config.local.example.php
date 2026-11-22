<?php
/**
 * Copy this file to app/config.local.php and fill in YOUR values.
 * app/config.local.php is in .gitignore: your passwords never go to GitHub.
 * Only put here what is different from app/config.php.
 */
return [
    'app' => [
        'url'            => 'http://localhost/paraxis',
        'dev_show_links' => true,   // shows the reason when an email fails (set false before the demo / production)
    ],

    // ---- Gmail (recommended) ----
    // 1. Turn on 2-Step Verification on the Google account.
    // 2. https://myaccount.google.com/apppasswords -> create an app password "Paraxis" -> copy the 16 letters.
    'mail' => [
        'driver'     => 'smtp',
        'host'       => 'smtp.gmail.com',
        'port'       => 587,
        'encryption' => 'tls',
        'username'   => 'yourteam.paraxis@gmail.com',
        'password'   => 'abcd efgh ijkl mnop',   // spaces are allowed, they are removed
        // Only if the test page says "certificate problem":
        // 'cafile'      => 'C:/xampp/apache/bin/curl-ca-bundle.crt',
        // 'verify_peer' => false,   // last resort, local development only
    ],

    // ---- or Mailtrap (fake inbox to test, nothing reaches real people) ----
    // 'mail' => ['host' => 'sandbox.smtp.mailtrap.io', 'port' => 2525, 'encryption' => 'tls',
    //            'username' => '...', 'password' => '...', 'from' => 'no-reply@paraxis.local'],

    // ---- Optional: Google reCAPTCHA instead of the built-in image ----
    // 'captcha'   => ['driver' => 'recaptcha'],
    // 'recaptcha' => ['site_key' => '...', 'secret_key' => '...'],
];
