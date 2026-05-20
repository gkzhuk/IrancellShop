<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;

class OrdersController extends BaseController
{
    public function index()
    {
        $model = new OrderModel();

        $paymentStatus = $this->request->getGet('status');
        $search = $this->request->getGet('search');
        $allowedStatuses = array_keys(OrderModel::paymentStatuses());

        if ($paymentStatus && in_array($paymentStatus, $allowedStatuses, true)) {
            $model->where('payment_status', $paymentStatus);
        }

        if ($search) {
            $model->groupStart()
                ->like('tracking_code', $search)
                ->orLike('buyer_name', $search)
                ->orLike('buyer_national_code', $search)
                ->orLike('buyer_phone', $search)
                ->orLike('authority', $search)
                ->orLike('ref_id', $search)
                ->groupEnd();
        }

        $model->select('orders.*, simcards.number as simcard_number');
        $model->join('simcards', 'simcards.id = orders.simcard_id', 'left');

        $data = [
            'orders'          => $model->orderBy('orders.created_at', 'DESC')->paginate(20),
            'pager'           => $model->pager,
            'status'          => $paymentStatus,
            'search'          => $search,
            'paymentStatuses' => OrderModel::paymentStatuses(),
        ];

        return view('admin/orders/index', $data);
    }
}
