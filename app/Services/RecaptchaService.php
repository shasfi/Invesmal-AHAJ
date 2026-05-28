<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaService
{
    public function isEnabled(): bool
    {
        return (bool) config('services.recaptcha.enabled')
            && filled(config('services.recaptcha.site_key'));
    }

    public function verify(?string $token, string $action = 'REGISTER'): bool
    {
        if (!$this->isEnabled()) {
            return true;
        }

        // FYP default: reCAPTCHA runs in browser; server API optional
        if (!config('services.recaptcha.verify_server', false)) {
            if (filled($token)) {
                return true;
            }

            Log::warning('RecaptchaService: missing client token', ['action' => $action]);

            return false;
        }

        if (empty($token)) {
            Log::warning('RecaptchaService: missing token', ['action' => $action]);

            return false;
        }

        $projectId = config('services.recaptcha.project_id', 'invesmal-37db0');
        $apiKey = config('services.recaptcha.api_key');
        $siteKey = config('services.recaptcha.site_key');

        if (!filled($apiKey)) {
            Log::warning('RecaptchaService: no API key; allowing (strict=false)');

            return !config('services.recaptcha.strict', false);
        }

        $url = "https://recaptchaenterprise.googleapis.com/v1/projects/{$projectId}/assessments?key={$apiKey}";

        try {
            $response = Http::timeout(10)->post($url, [
                'event' => [
                    'token' => $token,
                    'siteKey' => $siteKey,
                    'expectedAction' => $action,
                ],
            ]);

            if (!$response->successful()) {
                $body = $response->json() ?? [];
                $message = (string) ($body['error']['message'] ?? $response->body());
                $code = $body['error']['code'] ?? $response->status();

                Log::warning('RecaptchaService: API error', [
                    'code' => $code,
                    'message' => $message,
                    'action' => $action,
                ]);

                return $this->allowOnFailure();
            }

            $data = $response->json();
            $valid = $data['tokenProperties']['valid'] ?? false;
            $score = $data['riskAnalysis']['score'] ?? 0;
            $minScore = (float) config('services.recaptcha.min_score', 0.3);
            $passed = $valid && $score >= $minScore;

            if (!$passed) {
                Log::warning('RecaptchaService: score/valid failed', [
                    'valid' => $valid,
                    'score' => $score,
                    'min_score' => $minScore,
                ]);

                return $this->allowOnFailure();
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('RecaptchaService: ' . $e->getMessage());

            return $this->allowOnFailure();
        }
    }

    private function allowOnFailure(): bool
    {
        if (!config('services.recaptcha.strict', false)) {
            Log::info('RecaptchaService: non-strict mode — allowing register');

            return true;
        }

        return false;
    }
}
