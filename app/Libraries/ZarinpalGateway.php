<?php

namespace App\Libraries;

class ZarinpalGateway
{
    private string $merchantId;
    private bool $sandbox;
    private string $callbackUrl;

    public function __construct()
    {
        $this->merchantId  = (string) getenv('ZARINPAL_MERCHANT');
        $this->sandbox     = getenv('ZARINPAL_SANDBOX') === 'true';
        $this->callbackUrl = base_url('payment/callback');
    }

    public function request($amountToman, string $description, string $mobile = '', string $email = ''): array
    {
        $data = [
            'merchant_id'  => $this->merchantId,
            'amount'       => (int) $amountToman * 10, // Zarinpal amount is Rial
            'callback_url' => $this->callbackUrl,
            'description'  => $description,
            'metadata'     => [
                'mobile' => $mobile,
                'email'  => $email,
            ],
        ];

        $url = $this->sandbox
            ? 'https://sandbox.zarinpal.com/pg/v4/payment/request.json'
            : 'https://api.zarinpal.com/pg/v4/payment/request.json';

        $response = $this->sendRequest($url, $data);

        if (!$response['ok']) {
            return [
                'status'  => false,
                'type'    => 'technical_error',
                'message' => $response['message'],
                'raw'     => $response['raw'] ?? null,
            ];
        }

        $body = $response['body'];

        if (($body['data']['code'] ?? null) == 100 && !empty($body['data']['authority'])) {
            $authority = $body['data']['authority'];
            $paymentUrl = $this->sandbox
                ? "https://sandbox.zarinpal.com/pg/StartPay/{$authority}"
                : "https://www.zarinpal.com/pg/StartPay/{$authority}";

            return [
                'status'    => true,
                'authority' => $authority,
                'url'       => $paymentUrl,
                'raw'       => $body,
            ];
        }

        return [
            'status'  => false,
            'type'    => 'gateway_rejected',
            'code'    => $body['errors']['code'] ?? $body['data']['code'] ?? null,
            'message' => $body['errors']['message'] ?? 'Zarinpal payment request failed.',
            'raw'     => $body,
        ];
    }

    public function verify($amountToman, string $authority): array
    {
        $data = [
            'merchant_id' => $this->merchantId,
            'amount'      => (int) $amountToman * 10, // Zarinpal amount is Rial
            'authority'   => $authority,
        ];

        $url = $this->sandbox
            ? 'https://sandbox.zarinpal.com/pg/v4/payment/verify.json'
            : 'https://api.zarinpal.com/pg/v4/payment/verify.json';

        $response = $this->sendRequest($url, $data);

        if (!$response['ok']) {
            return [
                'status'  => false,
                'type'    => 'technical_error',
                'message' => $response['message'],
                'raw'     => $response['raw'] ?? null,
            ];
        }

        $body = $response['body'];
        $code = $body['data']['code'] ?? null;

        // 100 = verified successfully, 101 = already verified.
        // Treat both as successful/idempotent when ref_id exists.
        if (in_array((int) $code, [100, 101], true) && !empty($body['data']['ref_id'])) {
            return [
                'status' => true,
                'type'   => ((int) $code === 101 ? 'already_verified' : 'verified'),
                'code'   => (int) $code,
                'ref_id' => $body['data']['ref_id'],
                'raw'    => $body,
            ];
        }

        return [
            'status'  => false,
            'type'    => 'verify_failed',
            'code'    => $body['errors']['code'] ?? $code,
            'message' => $body['errors']['message'] ?? 'Zarinpal payment verification failed.',
            'raw'     => $body,
        ];
    }

    private function sendRequest(string $url, array $data): array
    {
        $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v4');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
        ]);

        $result   = curl_exec($ch);
        $curlErr  = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($curlErr) {
            return [
                'ok'      => false,
                'message' => 'Curl Error: ' . $curlErr,
                'raw'     => $result,
            ];
        }

        $body = json_decode((string) $result, true);

        if (!is_array($body)) {
            return [
                'ok'      => false,
                'message' => 'Invalid JSON response from Zarinpal.',
                'raw'     => $result,
            ];
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            return [
                'ok'      => false,
                'message' => 'HTTP Error: ' . $httpCode,
                'raw'     => $body,
            ];
        }

        return [
            'ok'   => true,
            'body' => $body,
        ];
    }
}
