<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class Recaptcha implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            // Log input value
            \Log::info('reCAPTCHA Input:', [
                'value_exists' => !empty($value),
                'value_length' => strlen($value),
                'site_key' => config('services.recaptcha.site_key'),
                // Jangan log full secret key untuk keamanan
                'secret_key_length' => strlen(config('services.recaptcha.secret_key')),
            ]);

            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $value,
                'ip' => request()->ip(),
            ]);

            // Log full response for debugging
            \Log::info('reCAPTCHA Full Response:', [
                'status_code' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->json(),
                'url' => $response->effectiveUri()->__toString(),
            ]);

            $responseData = $response->json();
            
            \Log::info('reCAPTCHA Response Data:', $responseData);

            // Perbaikan di sini - langsung return true jika success
            if ($responseData['success'] === true) {
                return; // Validasi berhasil
            }

            // Jika sampai di sini berarti validasi gagal
            $errorCodes = $responseData['error-codes'] ?? [];
            $errorMessage = $this->getErrorMessage($errorCodes);
            \Log::warning('reCAPTCHA Validation Failed', [
                'error_codes' => $errorCodes,
                'error_message' => $errorMessage
            ]);
            $fail($errorMessage);

        } catch (\Exception $e) {
            \Log::error('reCAPTCHA Exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            $fail('Terjadi kesalahan saat memverifikasi reCAPTCHA: ' . $e->getMessage());
        }
    }

    private function getErrorMessage(array $errorCodes): string
    {
        $errorMessages = [
            'missing-input-secret' => 'Secret key tidak ditemukan.',
            'invalid-input-secret' => 'Secret key tidak valid.',
            'missing-input-response' => 'Token reCAPTCHA tidak ditemukan.',
            'invalid-input-response' => 'Token reCAPTCHA tidak valid atau kadaluarsa.',
            'bad-request' => 'Request tidak valid.',
            'timeout-or-duplicate' => 'Token sudah kadaluarsa atau duplikat.',
        ];

        if (empty($errorCodes)) {
            return 'Verifikasi reCAPTCHA gagal: Unknown error';
        }

        $messages = [];
        foreach ($errorCodes as $code) {
            $messages[] = $errorMessages[$code] ?? $code;
        }

        return 'Verifikasi reCAPTCHA gagal: ' . implode(', ', $messages);
    }
} 