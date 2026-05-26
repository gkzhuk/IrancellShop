<?php

namespace App\Services;

use App\Models\SmsLogModel;

class SmsService
{
    private string $apiKey;
    private string $sender;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = (string) env('SMS_API_KEY', '');
        $this->sender = (string) env('SMS_SENDER', '');
        $this->baseUrl = rtrim((string) env('SMS_BASE_URL', ''), '/');
    }

    public function send(string $mobile, string $message, ?int $orderId = null, ?string $eventKey = null): bool
    {
        try {
            if ($this->apiKey === '' || $this->sender === '' || $this->baseUrl === '') {
                $this->log($mobile, $message, 'failed', 'SMS config missing', $orderId, $eventKey);
                return false;
            }

            $payload = json_encode([
                'from' => $this->sender,
                'to' => [$mobile],
                'message' => $message,
            ], JSON_UNESCAPED_UNICODE);

            $ch = curl_init($this->baseUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'apikey: ' . $this->apiKey,
                ],
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_TIMEOUT => 15,
            ]);

            $resp = curl_exec($ch);
            $err = curl_error($ch);
            $statusCode = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            curl_close($ch);

            if ($err || $statusCode < 200 || $statusCode >= 300) {
                $this->log($mobile, $message, 'failed', $err ?: (string) $resp, $orderId, $eventKey);
                return false;
            }

            $this->log($mobile, $message, 'sent', (string) $resp, $orderId, $eventKey);
            return true;
        } catch (\Throwable $e) {
            log_message('error', 'SMS send exception: {msg}', ['msg' => $e->getMessage()]);
            $this->log($mobile, $message, 'failed', $e->getMessage(), $orderId, $eventKey);
            return false;
        }
    }

    private function log(string $mobile, string $message, string $status, ?string $resp, ?int $orderId, ?string $eventKey): void
    {
        try {
            (new SmsLogModel())->insert([
                'mobile' => $mobile,
                'message' => $message,
                'status' => $status,
                'api_response' => $resp,
                'related_order_id' => $orderId,
                'event_key' => $eventKey,
                'sent_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'SMS log persist failed: {msg}', ['msg' => $e->getMessage()]);
        }
    }
}
