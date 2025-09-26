<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Str;

class AesEncrypted implements CastsAttributes
{
    private const CIPHER = 'AES-256-CBC';

    private string $key;

    public function __construct()
    {
        $appKey = config('app.key');
        if (Str::startsWith($appKey, 'base64:')) {
            $appKey = base64_decode(substr($appKey, 7), true) ?: '';
        }

        if (empty($appKey)) {
            throw new \RuntimeException('Application encryption key is missing.');
        }

        $this->key = substr(hash('sha256', $appKey, true), 0, 32);
    }

    public function get($model, string $key, $value, array $attributes): mixed
    {
        if (is_null($value) || $value === '') {
            return $value;
        }

        $decoded = base64_decode($value, true);
        if ($decoded === false) {
            return $value;
        }

        $ivLength = openssl_cipher_iv_length(self::CIPHER);
        if (strlen($decoded) <= $ivLength) {
            return $value;
        }

        $iv = substr($decoded, 0, $ivLength);
        $cipherText = substr($decoded, $ivLength);

        try {
            $decrypted = openssl_decrypt($cipherText, self::CIPHER, $this->key, OPENSSL_RAW_DATA, $iv);
        } catch (\Throwable $exception) {
            return $value;
        }

        return $decrypted === false ? $value : $decrypted;
    }

    public function set($model, string $key, $value, array $attributes): ?string
    {
        if (is_null($value) || $value === '') {
            return $value;
        }

        if (! is_string($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $ivLength = openssl_cipher_iv_length(self::CIPHER);
        $iv = random_bytes($ivLength);

        $encrypted = openssl_encrypt($value, self::CIPHER, $this->key, OPENSSL_RAW_DATA, $iv);

        if ($encrypted === false) {
            throw new \RuntimeException('Unable to encrypt attribute using AES-256-CBC.');
        }

        return base64_encode($iv . $encrypted);
    }
}