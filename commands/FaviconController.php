<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Encrypts/decrypts the favicon artwork using AES-256-CBC + HMAC-SHA256.
 *
 * The on-disk format mirrors the layout previously produced by
 * laminas/laminas-crypt FileCipher (encrypt-then-MAC), so existing
 * ikagirl.dat continues to decrypt unchanged:
 *
 *   [  0 ..  63] HMAC-SHA256, hex (64 chars)
 *   [ 64 ..  79] IV, 16 raw bytes
 *   [ 80 ..    ] AES-256-CBC ciphertext (PKCS7 padded)
 *
 * 64 bytes are derived from the license key via PBKDF2-SHA256
 * (iterations=65535, salt=IV); the first 32 bytes are the AES key, the
 * last 32 the HMAC key. The MAC is computed over "aes" || IV || ciphertext.
 */
class FaviconController extends Controller
{
    private const HASH_ALGO = 'sha256';
    private const CIPHER_ALGO = 'aes-256-cbc';
    private const HMAC_TAG = 'aes';
    private const PBKDF2_ITERATIONS = 65535;
    private const AES_KEY_SIZE = 32;
    private const IV_SIZE = 16;
    private const HMAC_HEX_SIZE = 64;

    public function actionEncrypt()
    {
        if (!$licenseKey = $this->readLicenseKey()) {
            $this->stdout("Favicon artwork license key is not exist (or broken).\n", Console::FG_RED);
            return 2;
        }

        $in = Yii::getAlias('@app/data/favicon/ikagirl.png');
        $out = Yii::getAlias('@app/data/favicon/ikagirl.dat');
        if (file_exists($out)) {
            $this->stdout("Output file already exists: {$out}\n", Console::FG_RED);
            return 1;
        }

        if (!$this->encryptFile($licenseKey, $in, $out)) {
            $this->stdout("Failed to create ikagirl.dat\n", Console::FG_RED);
            @unlink($out);
            return 1;
        }
        $this->stdout("Created ikagirl.dat\n", Console::FG_GREEN);
    }

    public function actionDecrypt()
    {
        if (!$licenseKey = $this->readLicenseKey()) {
            $this->stdout("SKIPPED (Favicon artwork license key is not exist or broken.)\n", Console::FG_YELLOW);
            return;
        }

        $in = Yii::getAlias('@app/data/favicon/ikagirl.dat');
        $out = Yii::getAlias('@app/data/favicon/ikagirl.png');
        @unlink($out);

        if (!$this->decryptFile($licenseKey, $in, $out)) {
            $this->stdout("Failed to create ikagirl.png\n", Console::FG_RED);
            @unlink($out);
            return 1;
        }
        $this->stdout("Created ikagirl.png\n", Console::FG_GREEN);
    }

    private function encryptFile(string $licenseKey, string $in, string $out): bool
    {
        $plaintext = @file_get_contents($in);
        if ($plaintext === false) {
            return false;
        }

        $iv = random_bytes(self::IV_SIZE);
        [$aesKey, $hmacKey] = $this->deriveKeys($licenseKey, $iv);

        $ciphertext = openssl_encrypt(
            $plaintext,
            self::CIPHER_ALGO,
            $aesKey,
            OPENSSL_RAW_DATA,
            $iv,
        );
        if ($ciphertext === false) {
            return false;
        }

        $hmac = hash_hmac(
            self::HASH_ALGO,
            self::HMAC_TAG . $iv . $ciphertext,
            $hmacKey,
        );

        return @file_put_contents($out, $hmac . $iv . $ciphertext, LOCK_EX) !== false;
    }

    private function decryptFile(string $licenseKey, string $in, string $out): bool
    {
        $blob = @file_get_contents($in);
        if ($blob === false || strlen($blob) <= self::HMAC_HEX_SIZE + self::IV_SIZE) {
            return false;
        }

        $storedHmac = substr($blob, 0, self::HMAC_HEX_SIZE);
        $iv = substr($blob, self::HMAC_HEX_SIZE, self::IV_SIZE);
        $ciphertext = substr($blob, self::HMAC_HEX_SIZE + self::IV_SIZE);

        [$aesKey, $hmacKey] = $this->deriveKeys($licenseKey, $iv);

        $expectedHmac = hash_hmac(
            self::HASH_ALGO,
            self::HMAC_TAG . $iv . $ciphertext,
            $hmacKey,
        );
        if (!hash_equals($expectedHmac, $storedHmac)) {
            return false;
        }

        $plaintext = openssl_decrypt(
            $ciphertext,
            self::CIPHER_ALGO,
            $aesKey,
            OPENSSL_RAW_DATA,
            $iv,
        );
        if ($plaintext === false) {
            return false;
        }

        return @file_put_contents($out, $plaintext, LOCK_EX) !== false;
    }

    /**
     * @return array{0: string, 1: string} [AES key (32 bytes), HMAC key (32 bytes)]
     */
    private function deriveKeys(string $licenseKey, string $iv): array
    {
        $derived = hash_pbkdf2(
            self::HASH_ALGO,
            $licenseKey,
            $iv,
            self::PBKDF2_ITERATIONS,
            self::AES_KEY_SIZE * 2,
            true,
        );
        return [
            substr($derived, 0, self::AES_KEY_SIZE),
            substr($derived, self::AES_KEY_SIZE),
        ];
    }

    private function readLicenseKey()
    {
        $path = Yii::getAlias('@app/config/favicon.license.txt');
        if (!file_exists($path) || !is_readable($path)) {
            return false;
        }
        $key = trim(file_get_contents($path, false, null));
        if (!preg_match('/^[!-~]{32}$/', $key)) {
            return false;
        }
        return $key;
    }
}
