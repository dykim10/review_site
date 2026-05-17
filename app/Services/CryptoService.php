<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CryptoService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.core_api.url');
    }

    public function hashEmail(string $email): string
    {
        return hash('sha256', strtolower(trim($email)));
    }

    public function encrypt(string $value): ?string
    {
        try {
            $response = Http::timeout(5)->post("{$this->baseUrl}/api/crypto/encrypt", [
                'value' => $value,
            ]);
            return $response->json('encrypted');
        } catch (\Exception $e) {
            Log::error('CryptoService encrypt 실패', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function decrypt(string $value): ?string
    {
        try {
            $response = Http::timeout(5)->post("{$this->baseUrl}/api/crypto/decrypt", [
                'value' => $value,
            ]);
            return $response->json('decrypted');
        } catch (\Exception $e) {
            Log::error('CryptoService decrypt 실패', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
