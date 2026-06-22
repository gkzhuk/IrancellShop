<?php

namespace App\Services;

use App\Models\SmsLogModel;

class SmsService
{
    private string $baseUrl;
    private string $username;
    private string $password;
    private string $sender;
    private ?string $token = null;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) env('SMS_BASE_URL', ''), '/');
        $this->username = (string) env('SMS_USERNAME', '');
        $this->password = (string) env('SMS_PASSWORD', '');
        $this->sender = (string) env('SMS_SENDER_NUMBER', '');
    }

    public function sendPattern(string $mobile, string $patternCode, array $params, ?int $orderId = null): bool
    {
        try {
            $mobile = $this->normalizePhone($mobile);
            if ($mobile === '' || $patternCode === '') {
                $this->logSms($orderId, $mobile, $patternCode, ['params' => $params], 'failed', 'invalid mobile/pattern');
                return false;
            }

            $token = $this->getToken();
            if ($token === null) {
                $this->logSms($orderId, $mobile, $patternCode, ['params' => $params], 'failed', 'auth_failed');
                return false;
            }

            $payload = [
                'sending_type' => 'pattern',
                'from_number' => $this->sender,
                'code' => $patternCode,
                'recipients' => [$mobile],
                'params' => $params,
            ];

            $result = $this->request('/api/send', $payload, $token);
            if ($result['status_code'] === 401) {
                if ($this->refreshToken()) {
                    $result = $this->request('/api/send', $payload, (string) $this->token);
                }
            }

            $ok = !$result['error'] && $result['status_code'] >= 200 && $result['status_code'] < 300;
            $this->logSms($orderId, $mobile, $patternCode, $payload, $ok ? 'sent' : 'failed', $result['error'] ?: $result['body']);
            return $ok;
        } catch (\Throwable $e) {
            log_message('error', 'SMS sendPattern exception: {msg}', ['msg' => $e->getMessage()]);
            $this->logSms($orderId, $mobile, $patternCode, ['params' => $params], 'failed', $e->getMessage());
            return false;
        }
    }

    public function normalizePhone(string $mobile): string
    {
        $m = preg_replace('/[^0-9+]/', '', trim($mobile));
        if (!$m) return '';
        if (str_starts_with($m, '+98')) return $m;
        if (str_starts_with($m, '0098')) return '+' . substr($m, 2);
        if (str_starts_with($m, '98')) return '+' . $m;
        if (str_starts_with($m, '0')) return '+98' . substr($m, 1);
        return '+98' . $m;
    }

    private function authenticate(): bool
    {
        if ($this->baseUrl === '' || $this->username === '' || $this->password === '' || $this->sender === '') {
            return false;
        }

        $result = $this->request('/api/acl/auth/login', [
            'username' => $this->username,
            'password' => $this->password,
        ]);

        if ($result['error'] || $result['status_code'] < 200 || $result['status_code'] >= 300) {
            return false;
        }

        $decoded = json_decode((string) $result['body'], true);
        if (!is_array($decoded)) {
            return false;
        }

        $method = (string) ($decoded['method'] ?? 'login');
        if (!in_array($method, ['login', 'sms', 'ga'], true)) {
            return false;
        }

        $token = (string) ($decoded['token'] ?? $decoded['data']['token'] ?? '');
        if ($token === '') {
            return false;
        }

        $this->token = $token;
        return true;
    }

    private function getToken(): ?string
    {
        if ($this->token) {
            return $this->token;
        }
        return $this->authenticate() ? $this->token : null;
    }

    private function refreshToken(): bool
    {
        $this->token = null;
        return $this->authenticate();
    }

    private function request(string $path, array $payload, ?string $token = null): array
    {
        $headers = ['Content-Type: application/json'];
        if ($token) $headers[] = 'Authorization: Bearer ' . $token;

        $ch = curl_init($this->baseUrl . $path);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
            CURLOPT_TIMEOUT => 15,
        ]);
        $body = curl_exec($ch);
        $error = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        return ['status_code' => $status, 'body' => (string) $body, 'error' => $error ?: null];
    }

    private function logSms(?int $orderId, string $mobile, string $patternCode, array $requestPayload, string $status, ?string $apiResponse): void
    {
        try {
            (new SmsLogModel())->insert([
                'related_order_id' => $orderId,
                'mobile' => $mobile,
                'pattern_code' => $patternCode,
                'request_payload' => json_encode($requestPayload, JSON_UNESCAPED_UNICODE),
                'api_response' => $apiResponse,
                'status' => $status,
                'sent_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'SMS log persist failed: {msg}', ['msg' => $e->getMessage()]);
        }
    }
}
