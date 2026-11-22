<?php

namespace App;

/**
 * MEMBER 2 - Sends emails (used by member 2 for the verification link, member 4 for the reset link /
 * "password changed" notice, and member 6 when the email or the password changes).
 *
 * Drivers (app/config.php -> 'mail.driver'):
 *   smtp : real sending through an SMTP server (Gmail, Outlook, Mailtrap...). No Composer / PHPMailer needed.
 *   mail : PHP mail() (needs sendmail configured in php.ini)
 *   log  : nothing is sent, the email is only saved
 * In every case the email is ALSO saved as an .html file in storage/mails/ (open it in the browser to debug).
 */
class Mailer
{
    /** Last error (empty when the last email was sent). Shown in the PHP error log, never to the visitor. */
    public static string $lastError = '';

    public static function send(string $to, string $subject, string $htmlBody): bool
    {
        self::$lastError = '';
        self::saveCopy($to, $subject, $htmlBody);

        if (!filter_var($to, FILTER_VALIDATE_EMAIL) || preg_match('/[\r\n]/', $to . $subject)) {
            return self::fail('Invalid recipient or subject.');
        }

        $driver = (string) config('mail.driver', 'log');
        try {
            if ($driver === 'smtp') {
                return self::sendSmtp($to, $subject, $htmlBody);
            }
            if ($driver === 'mail') {
                $headers = "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n"
                         . 'From: ' . self::fromHeader() . "\r\n";
                return @mail($to, self::encodeHeader($subject), $htmlBody, $headers) ?: self::fail('mail() returned false.');
            }
            return true; // 'log': saved in storage/mails only
        } catch (\Throwable $e) {
            return self::fail($e->getMessage());
        }
    }

    /** Simple branded email: title, text and one button */
    public static function template(string $title, string $text, string $buttonLabel, string $link): string
    {
        return '<div style="font-family:Arial,sans-serif;max-width:520px;margin:auto;padding:28px;border:1px solid #e8eee9;border-radius:14px">'
             . '<h2 style="color:#193f34;margin:0 0 14px">' . e($title) . '</h2>'
             . '<p style="color:#444;line-height:1.6">' . e($text) . '</p>'
             . '<p style="margin:26px 0"><a href="' . e($link) . '" style="background:#276b55;color:#fff;text-decoration:none;padding:13px 22px;border-radius:6px;font-weight:bold">' . e($buttonLabel) . '</a></p>'
             . '<p style="color:#888;font-size:12px">If the button does not work, copy this link into your browser:<br>' . e($link) . '</p>'
             . "<p style=\"color:#888;font-size:12px\">If you did not ask for this, you can ignore this email. - Par'Axis</p>"
             . '</div>';
    }

    /** Email sent after a password reset / change, so the owner knows if somebody else did it */
    public static function sendPasswordChanged(string $to, string $name): bool
    {
        $html = self::template(
            'Your password was changed',
            'Hello ' . $name . ', the password of your Par\'Axis account was changed on ' . date('d/m/Y \a\t H:i')
                . '. If it was you, there is nothing to do. If it was NOT you, change your password right away and contact our support.',
            'Log in to my account',
            baseUrl() . '/index.php?page=login'
        );
        return self::send($to, "Your Par'Axis password was changed", $html);
    }

    // ------------------------------------------------------------------ SMTP

    private static function sendSmtp(string $to, string $subject, string $htmlBody): bool
    {
        $host     = (string) config('mail.host');
        $port     = (int) config('mail.port', 587);
        $enc      = strtolower((string) config('mail.encryption', 'tls'));
        $user     = trim((string) config('mail.username'));
        $pass     = str_replace(' ', '', (string) config('mail.password')); // Gmail shows app passwords as "abcd efgh ijkl mnop"
        $timeout  = (int) config('mail.timeout', 15);

        if ($host === '' || $user === '' || $pass === '') {
            return self::fail('SMTP is not configured: create app/config.local.php (copy app/config.local.example.php) and fill mail.username and mail.password.');
        }
        if (!extension_loaded('openssl') && $enc !== '') {
            return self::fail('The PHP "openssl" extension is disabled: remove the ";" before "extension=openssl" in C:\\xampp\\php\\php.ini, then restart Apache.');
        }

        // try the configured port first; Gmail / Outlook: if 587 (tls) is blocked, try 465 (ssl) and the other way round
        $attempts = [[$port, $enc]];
        if ($enc === 'tls' && $port === 587) {
            $attempts[] = [465, 'ssl'];
        } elseif ($enc === 'ssl' && $port === 465) {
            $attempts[] = [587, 'tls'];
        }

        $errors = [];
        foreach ($attempts as [$p, $e]) {
            $result = self::smtpSession($host, $p, $e, $user, $pass, $timeout, $to, $subject, $htmlBody);
            if ($result === true) {
                return true;
            }
            $errors[] = "port $p/$e: $result";
            if (str_contains($result, 'Authentication refused') || str_contains($result, 'RCPT')) {
                break; // the server answered: changing the port will not help
            }
        }
        return self::fail(implode(' | ', $errors));
    }

    /** One SMTP conversation. Returns true, or the error message. */
    private static function smtpSession(string $host, int $port, string $enc, string $user, string $pass, int $timeout,
                                        string $to, string $subject, string $htmlBody)
    {
        $context = stream_context_create(['ssl' => self::sslOptions($host)]);
        $remote  = ($enc === 'ssl' ? 'ssl://' : 'tcp://') . $host . ':' . $port;
        $socket  = @stream_socket_client($remote, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $context);
        if (!$socket) {
            $ssl = self::opensslErrors();
            return "Cannot connect to $remote: " . trim("$errstr ($errno) $ssl")
                 . ($ssl !== '' ? ' - certificate problem: see mail.cafile in app/config.local.example.php' : ' - port blocked by the network / antivirus?');
        }
        stream_set_timeout($socket, $timeout);

        try {
            $helo = gethostname() ?: 'localhost';
            self::expect($socket, 220);
            self::command($socket, 'EHLO ' . $helo, 250);

            if ($enc === 'tls') {
                self::command($socket, 'STARTTLS', 220);
                $crypto = STREAM_CRYPTO_METHOD_TLS_CLIENT;
                if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) {
                    $crypto |= STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
                }
                if (!@stream_socket_enable_crypto($socket, true, $crypto)) {
                    throw new \RuntimeException('STARTTLS failed ' . self::opensslErrors()
                        . ' - certificate problem: see mail.cafile in app/config.local.example.php');
                }
                self::command($socket, 'EHLO ' . $helo, 250);
            }

            self::command($socket, 'AUTH LOGIN', 334);
            self::command($socket, base64_encode($user), 334, '', 'AUTH username');
            self::command($socket, base64_encode($pass), 235,
                'Authentication refused: for Gmail use a 16-letter App password (myaccount.google.com/apppasswords), not your normal password.', 'AUTH password');

            $from = self::fromAddress();
            self::command($socket, 'MAIL FROM:<' . $from . '>', 250);
            self::command($socket, 'RCPT TO:<' . $to . '>', [250, 251]);
            self::command($socket, 'DATA', 354);

            fwrite($socket, self::buildMessage($from, $to, $subject, $htmlBody) . "\r\n.\r\n");
            self::expect($socket, 250, '', 'message');
            @fwrite($socket, "QUIT\r\n");
        } catch (\RuntimeException $e) {
            @fwrite($socket, "QUIT\r\n");
            fclose($socket);
            return $e->getMessage();
        }
        fclose($socket);
        return true;
    }

    /**
     * Certificate options. XAMPP on Windows often has no CA list configured, so PHP cannot check Gmail's certificate.
     * We use, in this order: mail.cafile, php.ini openssl.cafile, or a CA file shipped with XAMPP.
     */
    private static function sslOptions(string $host): array
    {
        if (config('mail.verify_peer', true) === false) { // last resort, local development only
            return ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true];
        }
        $options = ['verify_peer' => true, 'verify_peer_name' => true, 'peer_name' => $host];
        $cafile  = (string) config('mail.cafile', '');
        if ($cafile === '' && (string) ini_get('openssl.cafile') === '') {
            $xampp = dirname(PHP_BINARY);                       // e.g. C:\xampp\php
            foreach ([
                $xampp . '/extras/ssl/cacert.pem',
                dirname($xampp) . '/apache/bin/curl-ca-bundle.crt',
                dirname($xampp) . '/php/extras/ssl/cacert.pem',
                'C:/xampp/php/extras/ssl/cacert.pem',
                'C:/xampp/apache/bin/curl-ca-bundle.crt',
            ] as $candidate) {
                if (is_file($candidate)) {
                    $cafile = $candidate;
                    break;
                }
            }
        }
        if ($cafile !== '' && is_file($cafile)) {
            $options['cafile'] = $cafile;
        }
        return $options;
    }

    private static function opensslErrors(): string
    {
        $out = [];
        while (function_exists('openssl_error_string') && ($msg = openssl_error_string()) !== false) {
            $out[] = $msg;
        }
        return $out ? '(' . implode('; ', $out) . ')' : '';
    }

    /** Sends one SMTP command and checks the reply code */
    private static function command($socket, string $line, $codes, string $hint = '', string $label = ''): string
    {
        fwrite($socket, $line . "\r\n");
        return self::expect($socket, $codes, $hint, $label !== '' ? $label : $line); // label hides the login / password
    }

    /** Reads a (possibly multi-line) SMTP reply and checks its code */
    private static function expect($socket, $codes, string $hint = '', string $sent = ''): string
    {
        $reply = '';
        while (($line = fgets($socket, 515)) !== false) {
            $reply .= $line;
            if (strlen($line) < 4 || $line[3] !== '-') { // "250-..." = more lines follow, "250 ..." = last line
                break;
            }
        }
        $code = (int) substr($reply, 0, 3);
        if (!in_array($code, (array) $codes, true)) {
            $meta = stream_get_meta_data($socket);
            $why  = $reply === '' ? ($meta['timed_out'] ? 'timeout' : 'connection closed') : trim($reply);
            throw new \RuntimeException(trim("SMTP error after \"$sent\": $why. $hint"));
        }
        return $reply;
    }

    private static function buildMessage(string $from, string $to, string $subject, string $htmlBody): string
    {
        $boundary = 'b' . bin2hex(random_bytes(12));
        $domain   = substr(strrchr($from, '@'), 1) ?: 'paraxis.local';
        $text     = trim(html_entity_decode(strip_tags(str_replace(['<br>', '</p>', '</h2>'], "\n", $htmlBody)), ENT_QUOTES, 'UTF-8'));

        $headers = [
            'Date: ' . date('r'),
            'From: ' . self::fromHeader(),
            'To: <' . $to . '>',
            'Subject: ' . self::encodeHeader($subject),
            'Message-ID: <' . bin2hex(random_bytes(16)) . '@' . $domain . '>',
            'MIME-Version: 1.0',
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
        ];
        $body = '--' . $boundary . "\r\n"
              . "Content-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n"
              . chunk_split(base64_encode($text))
              . '--' . $boundary . "\r\n"
              . "Content-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n"
              . chunk_split(base64_encode($htmlBody))
              . '--' . $boundary . '--';

        return implode("\r\n", $headers) . "\r\n\r\n" . $body; // base64 lines never start with ".", no dot-stuffing needed
    }

    // ------------------------------------------------------------------ helpers

    private static function fromAddress(): string
    {
        $from = (string) config('mail.from');
        return filter_var($from, FILTER_VALIDATE_EMAIL) ? $from : (string) config('mail.username');
    }

    private static function fromHeader(): string
    {
        return self::encodeHeader((string) config('mail.from_name', "Par'Axis")) . ' <' . self::fromAddress() . '>';
    }

    private static function encodeHeader(string $value): string
    {
        return '=?UTF-8?B?' . base64_encode($value) . '?=';
    }

    private static function saveCopy(string $to, string $subject, string $htmlBody): void
    {
        $dir = __DIR__ . '/../storage/mails';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        @file_put_contents(
            $dir . '/' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.html',
            '<!-- To: ' . e($to) . ' | Subject: ' . e($subject) . " -->\n" . $htmlBody
        );
    }

    private static function fail(string $message): bool
    {
        self::$lastError = $message;
        error_log('[Paraxis Mailer] ' . $message);
        return false;
    }
}
