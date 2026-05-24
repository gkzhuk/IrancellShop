<?php

namespace App\Controllers;

use App\Libraries\ZarinpalGateway;
use App\Models\OrderModel;
use App\Models\SimcardModel;

class PaymentController extends BaseController
{
    private ?array $ordersTableColumns = null;

    public function start()
    {
        $simcardId = $this->request->getPost('simcard_id');
        $simcardModel = new SimcardModel();
        $simcard = $simcardModel->find($simcardId);

        if (!$simcard || $simcard['status'] !== 'free') {
            return redirect()->to('/')->with('error', 'سیم‌کارت نامعتبر یا فروخته شده است.');
        }

        if (!$this->validate([
            'buyer_name'          => 'required',
            'buyer_national_code' => 'required|exact_length[10]',
            'buyer_phone'         => 'required|exact_length[11]',
            'father_name'         => 'required',
            'birth_year'          => 'required',
            'birth_month'         => 'required',
            'birth_day'           => 'required',
            'rules'               => 'required',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $amountRial  = (int) $simcard['price'];
        $amountToman = (int) ($amountRial / 10);
        $trackingCode = $this->generateTrackingCode();
        $birthdate = $this->request->getPost('birth_year') . '-' .
            $this->request->getPost('birth_month') . '-' .
            $this->request->getPost('birth_day');

        $orderModel = new OrderModel();

        $insertData = [
            'tracking_code'       => $trackingCode,
            'simcard_id'          => $simcardId,
            'buyer_name'          => $this->request->getPost('buyer_name'),
            'buyer_national_code' => $this->request->getPost('buyer_national_code'),
            'buyer_phone'         => $this->request->getPost('buyer_phone'),
            'buyer_father_name'   => $this->request->getPost('father_name'),
            'buyer_birthdate'     => $birthdate,
            'amount'              => $amountRial,
            'payment_status'      => OrderModel::STATUS_PENDING,
            'payment_message'     => 'Order created. Waiting for gateway request.',
        ];

        if ($this->ordersColumnExists('secure_token')) {
            $insertData['secure_token'] = bin2hex(random_bytes(32));
        }
        if ($this->ordersColumnExists('admin_status')) {
            $insertData['admin_status'] = OrderModel::ADMIN_STATUS_DOCUMENTS_PENDING;
        }

        $orderId = $orderModel->insert($insertData, true);

        $zarinpal = new ZarinpalGateway();
        $description = 'خرید سیم‌کارت ' . $simcard['number'];
        $response = $zarinpal->request($amountToman, $description, $this->request->getPost('buyer_phone'));

        if ($response['status']) {
            $orderModel->update($orderId, [
                'authority'       => $response['authority'],
                'payment_status'  => OrderModel::STATUS_GATEWAY_REQUESTED,
                'payment_message' => 'Authority received. User redirected to Zarinpal.',
            ]);

            return redirect()->to($response['url']);
        }

        // Payment request was not created, so this is a definitive request-level failure.
        $orderModel->update($orderId, [
            'payment_status'  => OrderModel::STATUS_FAILED,
            'payment_message' => $response['message'] ?? 'Gateway request failed.',
        ]);

        return redirect()->back()->with('error', 'خطا در اتصال به درگاه پرداخت: ' . ($response['message'] ?? 'خطای نامشخص'));
    }

    public function callback()
    {
        $authority = (string) $this->request->getGet('Authority');
        $status    = (string) $this->request->getGet('Status');

        if ($authority === '') {
            return view('front/failed', ['message' => 'شناسه پرداخت ارسال نشده است.']);
        }

        $orderModel = new OrderModel();
        $order = $orderModel->where('authority', $authority)->first();

        if (!$order) {
            return view('front/failed', ['message' => 'سفارش یافت نشد.']);
        }

        // Idempotency guard: never downgrade a successful payment.
        if ($order['payment_status'] === OrderModel::STATUS_SUCCESS) {
            if ($this->ordersColumnExists('secure_token') && empty($order['secure_token'])) {
                $newToken = bin2hex(random_bytes(32));
                $orderModel->update($order['id'], ['secure_token' => $newToken]);
                $order['secure_token'] = $newToken;
            }

            return view('front/success', [
                'order'  => $order,
                'ref_id' => $order['ref_id'],
            ]);
        }

        // User canceled, left gateway, or payment was not completed.
        // This is NOT a verified failed payment.
        if ($status !== 'OK') {
            $this->updateOrderIfNotSuccess($orderModel, (int) $order['id'], [
                'payment_status'  => OrderModel::STATUS_CANCELED,
                'payment_message' => 'Callback returned with non-OK status: ' . ($status ?: 'EMPTY'),
            ]);

            return view('front/failed', ['message' => 'پرداخت تکمیل نشد. در صورت کسر وجه، نتیجه پس از بررسی بانک مشخص می‌شود.']);
        }

        // Mark as verifying before external call. If verify times out, admin can see it needs retry.
        $this->updateOrderIfNotSuccess($orderModel, (int) $order['id'], [
            'payment_status'  => OrderModel::STATUS_VERIFY_PENDING,
            'payment_message' => 'Callback OK received. Verifying payment with Zarinpal.',
        ]);

        $amountToman = (int) ($order['amount'] / 10);
        $zarinpal = new ZarinpalGateway();
        $verification = $zarinpal->verify($amountToman, $authority);

        if ($verification['status']) {
            $db = \Config\Database::connect();
            $db->transStart();

            $freshOrder = $orderModel->where('id', $order['id'])->first();

            if ($freshOrder && $freshOrder['payment_status'] !== OrderModel::STATUS_SUCCESS) {
                $successUpdate = [
                    'payment_status'      => OrderModel::STATUS_SUCCESS,
                    'ref_id'              => $verification['ref_id'],
                    'payment_message'     => 'Payment verified successfully. Verify type: ' . ($verification['type'] ?? 'verified'),
                    'payment_verified_at' => date('Y-m-d H:i:s'),
                ];
                if ($this->ordersColumnExists('admin_status')) {
                    $successUpdate['admin_status'] = OrderModel::ADMIN_STATUS_DOCUMENTS_PENDING;
                }
                if ($this->ordersColumnExists('secure_token') && empty($freshOrder['secure_token'])) {
                    $successUpdate['secure_token'] = bin2hex(random_bytes(32));
                }

                $orderModel->update($order['id'], $successUpdate);

                if (isset($successUpdate['secure_token'])) {
                    $order['secure_token'] = $successUpdate['secure_token'];
                } elseif (!empty($freshOrder['secure_token'])) {
                    $order['secure_token'] = $freshOrder['secure_token'];
                }

                $simcardModel = new SimcardModel();
                $simcardModel->update($order['simcard_id'], ['status' => 'sold']);
            }

            $db->transComplete();

            $order['payment_status'] = OrderModel::STATUS_SUCCESS;
            $order['ref_id'] = $verification['ref_id'];

            return view('front/success', [
                'order'  => $order,
                'ref_id' => $verification['ref_id'],
            ]);
        }

        // Technical errors must not become definitive failed payments.
        // Example: curl timeout, invalid JSON, temporary Zarinpal/server issue.
        if (($verification['type'] ?? null) === 'technical_error') {
            $this->updateOrderIfNotSuccess($orderModel, (int) $order['id'], [
                'payment_status'  => OrderModel::STATUS_VERIFY_PENDING,
                'payment_message' => 'Technical verify error: ' . ($verification['message'] ?? 'Unknown technical error'),
            ]);

            return view('front/failed', ['message' => 'نتیجه پرداخت در حال بررسی است. لطفاً بعداً وضعیت سفارش را پیگیری کنید.']);
        }

        // Definitive verify failure from gateway.
        $this->updateOrderIfNotSuccess($orderModel, (int) $order['id'], [
            'payment_status'  => OrderModel::STATUS_VERIFY_FAILED,
            'payment_message' => 'Verify failed: ' . ($verification['message'] ?? 'Unknown verify failure'),
        ]);

        return view('front/failed', ['message' => 'پرداخت توسط درگاه تأیید نشد.']);
    }

    private function updateOrderIfNotSuccess(OrderModel $orderModel, int $orderId, array $data): bool
    {
        return (bool) $orderModel
            ->where('id', $orderId)
            ->where('payment_status !=', OrderModel::STATUS_SUCCESS)
            ->set($data)
            ->update();
    }

    private function generateTrackingCode(): string
    {
        do {
            $code = (string) rand(100000, 999999);
            $exists = (new OrderModel())->where('tracking_code', $code)->countAllResults();
        } while ($exists > 0);

        return $code;
    }

    private function ordersColumnExists(string $column): bool
    {
        if ($this->ordersTableColumns === null) {
            $this->ordersTableColumns = \Config\Database::connect()->getFieldNames('orders');
        }

        return in_array($column, $this->ordersTableColumns, true);
    }
}
