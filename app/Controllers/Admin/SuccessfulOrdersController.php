<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Services\SmsService;

class SuccessfulOrdersController extends BaseController
{
    private ?array $ordersTableColumns = null;

    public function index()
    {
        $model = new OrderModel();
        $search = $this->request->getGet('search');

        $select = 'orders.*, simcards.number as simcard_number';
        if (!$this->ordersColumnExists('order_status')) {
            $select .= ", 'documents_pending' as order_status";
        }

        $model->where('orders.payment_status', OrderModel::STATUS_SUCCESS)
            ->select($select)
            ->join('simcards', 'simcards.id = orders.simcard_id', 'left');

        if ($search) {
            $model->groupStart()->like('tracking_code', $search)->orLike('buyer_name', $search)->orLike('buyer_phone', $search)->groupEnd();
        }

        return view('admin/successful_orders/index', [
            'orders' => $model->orderBy('orders.created_at', 'DESC')->paginate(20),
            'pager' => $model->pager,
            'search' => $search,
            'orderStatuses' => OrderModel::adminStatuses(),
        ]);
    }

    public function show(int $id)
    {
        $order = (new OrderModel())
            ->where('orders.id', $id)
            ->where('orders.payment_status', OrderModel::STATUS_SUCCESS)
            ->select($this->ordersColumnExists('order_status') ? 'orders.*, simcards.number as simcard_number' : "orders.*, simcards.number as simcard_number, 'documents_pending' as order_status")
            ->join('simcards', 'simcards.id = orders.simcard_id', 'left')
            ->first();

        if (!$order) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('admin/successful_orders/show', ['order' => $order, 'orderStatuses' => OrderModel::adminStatuses()]);
    }


    public function document(int $id, string $type)
    {
        if (!in_array($type, ['national', 'selfie'], true)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $order = (new OrderModel())
            ->where('id', $id)
            ->where('payment_status', OrderModel::STATUS_SUCCESS)
            ->first();

        if (!$order) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $relativePath = $type === 'national'
            ? (($order['national_id_document_path'] ?? '') ?: ($order['national_card_image'] ?? ''))
            : (($order['selfie_document_path'] ?? '') ?: ($order['selfie_image'] ?? ''));

        $relativePath = trim((string) $relativePath);
        if ($relativePath === '') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $fullPath = WRITEPATH . 'secure_uploads/' . str_replace(['..', '\\'], '', $relativePath);
        if (!is_file($fullPath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $mime = mime_content_type($fullPath) ?: 'application/octet-stream';
        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'inline; filename="' . basename($fullPath) . '"')
            ->setBody((string) file_get_contents($fullPath));
    }

    public function updateStatus(int $id)
    {
        $status = (string) $this->request->getPost('order_status');
        if (!$this->ordersColumnExists('order_status')) {
            return redirect()->back()->with('error', 'ستون وضعیت سفارش ایجاد نشده است.');
        }
        if (!array_key_exists($status, OrderModel::adminStatuses())) {
            return redirect()->back()->with('error', 'وضعیت نامعتبر است.');
        }
        $orderModel = new OrderModel();
        $orderModel->update($id, ['order_status' => $status]);
        $order = $orderModel->find($id);
        if ($order) {
            $message = $this->statusSmsMessage($status, $order);
            if ($message !== '') {
                try {
                    (new SmsService())->send((string) ($order['buyer_phone'] ?? ''), $message, (int) $id, 'status_' . $status);
                } catch (\Throwable $e) {
                    log_message('error', 'SMS status send failed: {msg}', ['msg' => $e->getMessage()]);
                }
            }
        }
        return redirect()->back()->with('success', 'وضعیت سفارش به‌روزرسانی شد.');
    }

    private function ordersColumnExists(string $column): bool
    {
        if ($this->ordersTableColumns === null) {
            $this->ordersTableColumns = \Config\Database::connect()->getFieldNames('orders');
        }

        return in_array($column, $this->ordersTableColumns, true);
    }
}
