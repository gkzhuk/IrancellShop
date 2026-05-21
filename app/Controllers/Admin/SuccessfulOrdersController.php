<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;

class SuccessfulOrdersController extends BaseController
{
    public function index()
    {
        $model = new OrderModel();
        $search = $this->request->getGet('search');

        $model->where('orders.payment_status', OrderModel::STATUS_SUCCESS)
            ->select('orders.*, simcards.number as simcard_number')
            ->join('simcards', 'simcards.id = orders.simcard_id', 'left');

        if ($search) {
            $model->groupStart()->like('tracking_code', $search)->orLike('buyer_name', $search)->orLike('buyer_phone', $search)->groupEnd();
        }

        return view('admin/successful_orders/index', [
            'orders' => $model->orderBy('orders.created_at', 'DESC')->paginate(20),
            'pager' => $model->pager,
            'search' => $search,
            'adminStatuses' => OrderModel::adminStatuses(),
        ]);
    }

    public function show(int $id)
    {
        $order = (new OrderModel())
            ->where('orders.id', $id)
            ->where('orders.payment_status', OrderModel::STATUS_SUCCESS)
            ->select('orders.*, simcards.number as simcard_number')
            ->join('simcards', 'simcards.id = orders.simcard_id', 'left')
            ->first();

        if (!$order) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('admin/successful_orders/show', ['order' => $order, 'adminStatuses' => OrderModel::adminStatuses()]);
    }

    public function updateStatus(int $id)
    {
        $status = (string) $this->request->getPost('admin_status');
        if (!array_key_exists($status, OrderModel::adminStatuses())) {
            return redirect()->back()->with('error', 'وضعیت نامعتبر است.');
        }
        (new OrderModel())->update($id, ['admin_status' => $status]);
        return redirect()->back()->with('success', 'وضعیت سفارش به‌روزرسانی شد.');
    }
}
