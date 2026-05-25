<?php

namespace App\Controllers;

use App\Models\OrderModel;

class OrderCompletionController extends BaseController
{
    private ?array $ordersTableColumns = null;

    public function show(string $token)
    {
        if (!$this->ordersColumnExists('secure_token')) {
            return $this->invalidLink('این سرویس هنوز فعال نشده است.');
        }

        $order = (new OrderModel())->where('secure_token', $token)->where('payment_status', OrderModel::STATUS_SUCCESS)->first();
        if (!$order) return $this->invalidLink('لینک نامعتبر است.');
        if (($order['admin_status'] ?? '') === OrderModel::ADMIN_STATUS_CANCELLED) return $this->invalidLink('این سفارش لغو شده است.');
        return view('front/complete_order', ['order' => $order, 'readonly' => ($order['admin_status'] ?? '') === OrderModel::ADMIN_STATUS_COMPLETED]);
    }

    public function submit(string $token)
    {
        if (!$this->ordersColumnExists('secure_token')) {
            return redirect()->back()->with('error', 'این سرویس هنوز فعال نشده است.');
        }

        $model = new OrderModel();
        $order = $model->where('secure_token', $token)->where('payment_status', OrderModel::STATUS_SUCCESS)->first();
        if (!$order) return redirect()->back()->with('error', 'لینک نامعتبر است.');
        if (($order['admin_status'] ?? '') === OrderModel::ADMIN_STATUS_COMPLETED) return redirect()->back()->with('success', 'این سفارش قبلاً تکمیل شده است.');
        if (($order['admin_status'] ?? '') === OrderModel::ADMIN_STATUS_CANCELLED) return redirect()->back()->with('error', 'این سفارش لغو شده است.');

        $rules = [
            'address' => 'required|min_length[10]',
            'postal_code' => 'required|regex_match[/^[0-9]{10}$/]',
            'national_id_document' => 'uploaded[national_id_document]|mime_in[national_id_document,image/jpeg,image/png,application/pdf]|max_size[national_id_document,5120]',
            'selfie_document' => 'uploaded[selfie_document]|mime_in[selfie_document,image/jpeg,image/png,application/pdf]|max_size[selfie_document,5120]',
        ];
        if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

        $dir = WRITEPATH . 'secure_uploads/orders/' . $order['id']; if (!is_dir($dir)) mkdir($dir, 0750, true);
        $nid = $this->request->getFile('national_id_document'); $sf = $this->request->getFile('selfie_document');
        $nidName = $nid->getRandomName(); $sfName = $sf->getRandomName();
        $nid->move($dir, $nidName); $sf->move($dir, $sfName);

        $updateData = [
            'address' => $this->request->getPost('address'),
            'postal_code' => $this->request->getPost('postal_code'),
            'national_id_document_path' => 'orders/' . $order['id'] . '/' . $nidName,
            'selfie_document_path' => 'orders/' . $order['id'] . '/' . $sfName,
        ];
        if ($this->ordersColumnExists('documents_uploaded_at')) {
            $updateData['documents_uploaded_at'] = date('Y-m-d H:i:s');
        }
        if ($this->ordersColumnExists('admin_status')) {
            $updateData['admin_status'] = OrderModel::ADMIN_STATUS_DOCUMENTS_UPLOADED;
        }

        $model->update($order['id'], $updateData);

        return redirect()->back()->with('success', 'اطلاعات با موفقیت ثبت شد.');
    }


    private function invalidLink(string $message)
    {
        return view('front/document_link_invalid', ['message' => $message]);
    }

    private function ordersColumnExists(string $column): bool
    {
        if ($this->ordersTableColumns === null) {
            $this->ordersTableColumns = \Config\Database::connect()->getFieldNames('orders');
        }

        return in_array($column, $this->ordersTableColumns, true);
    }
}
