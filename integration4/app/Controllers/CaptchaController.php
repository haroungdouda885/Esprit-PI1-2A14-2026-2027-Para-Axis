<?php

namespace App\Controllers;

use App\Security;

/**
 * MEMBER 5 - Route "captcha": draws the security image used on login / sign up / forgot password.
 * The code itself is only kept (hashed) in the session: it is never written in the page.
 * Uses the GD extension (enabled by default in XAMPP); without GD a distorted SVG is sent instead.
 */
class CaptchaController extends Controller
{
    private const W = 180;
    private const H = 60;

    public function image(): void
    {
        $code = Security::newCaptchaCode();

        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('X-Content-Type-Options: nosniff');

        if (function_exists('imagecreatetruecolor')) {
            $this->png($code);
        } else {
            $this->svg($code);
        }
        exit;
    }

    private function png(string $code): void
    {
        $img = imagecreatetruecolor(self::W, self::H);
        imagefill($img, 0, 0, imagecolorallocate($img, 246, 250, 246));

        // background noise: dots and curves
        for ($i = 0; $i < 350; $i++) {
            $c = imagecolorallocate($img, random_int(150, 220), random_int(170, 225), random_int(150, 220));
            imagesetpixel($img, random_int(0, self::W - 1), random_int(0, self::H - 1), $c);
        }
        for ($i = 0; $i < 5; $i++) {
            $c = imagecolorallocate($img, random_int(120, 190), random_int(140, 200), random_int(120, 190));
            imagesetthickness($img, random_int(1, 2));
            imagearc($img, random_int(0, self::W), random_int(0, self::H), random_int(80, 260), random_int(30, 120), random_int(0, 360), random_int(0, 360), $c);
        }

        // each letter: drawn with the built-in font, enlarged, rotated and placed at a random height
        $step = (self::W - 20) / strlen($code);
        foreach (str_split($code) as $i => $ch) {
            $tile = imagecreatetruecolor(13, 16);
            $bg   = imagecolorallocate($tile, 246, 250, 246);
            imagefill($tile, 0, 0, $bg);
            imagecolortransparent($tile, $bg);
            $ink = imagecolorallocate($tile, random_int(15, 50), random_int(60, 100), random_int(40, 75));
            imagestring($tile, 5, 1, 0, $ch, $ink);
            imagestring($tile, 5, 2, 0, $ch, $ink); // drawn twice = bold, easier for humans to read

            $size = random_int(28, 32);
            $big  = imagecreatetruecolor($size, (int) ($size * 1.3));
            imagefill($big, 0, 0, $bg2 = imagecolorallocate($big, 246, 250, 246));
            imagecolortransparent($big, $bg2);
            imagecopyresized($big, $tile, 0, 0, 0, 0, $size, (int) ($size * 1.3), 13, 16);
            $rot = imagerotate($big, random_int(-15, 15), $bg2);
            imagecolortransparent($rot, imagecolorat($rot, 0, 0));

            $x = (int) (10 + $i * $step + random_int(-3, 3));
            $y = random_int(0, max(0, self::H - imagesy($rot)));
            imagecopymerge($img, $rot, $x, $y, 0, 0, imagesx($rot), imagesy($rot), 100);
            imagedestroy($tile);
            imagedestroy($big);
            imagedestroy($rot);
        }

        // lines over the letters
        for ($i = 0; $i < 2; $i++) {
            imagesetthickness($img, 1);
            imageline($img, 0, random_int(10, self::H - 10), self::W, random_int(10, self::H - 10),
                imagecolorallocate($img, random_int(60, 130), random_int(100, 150), random_int(80, 130)));
        }

        header('Content-Type: image/png');
        imagepng($img);
        imagedestroy($img);
    }

    private function svg(string $code): void
    {
        $out  = '<svg xmlns="http://www.w3.org/2000/svg" width="' . self::W . '" height="' . self::H . '" viewBox="0 0 ' . self::W . ' ' . self::H . '">';
        $out .= '<rect width="100%" height="100%" fill="#f6faf6"/>';
        for ($i = 0; $i < 6; $i++) {
            $out .= sprintf('<path d="M0 %d Q %d %d %d %d" stroke="#9fbfae" stroke-width="%d" fill="none"/>',
                random_int(5, 55), random_int(40, 140), random_int(-20, 80), self::W, random_int(5, 55), random_int(1, 2));
        }
        $step = (self::W - 20) / strlen($code);
        foreach (str_split($code) as $i => $ch) {
            $x = 14 + $i * $step;
            $y = random_int(38, 48);
            $out .= sprintf('<text x="%.1f" y="%d" font-family="monospace" font-size="%d" font-weight="bold" fill="#1d5a45" transform="rotate(%d %.1f %d) skewX(%d)">%s</text>',
                $x, $y, random_int(28, 34), random_int(-25, 25), $x, $y, random_int(-12, 12), $ch);
        }
        $out .= '</svg>';
        header('Content-Type: image/svg+xml');
        echo $out;
    }
}
