<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\IpWhitelistModel;

/**
 * Admin panel controller to manage the API IP whitelist.
 * Protected by the adminAuth filter like all other admin controllers.
 */
class IpWhitelistController extends BaseController
{
    public function index()
    {
        $model = new IpWhitelistModel();
        return view('admin/ip_whitelist/index', [
            'ips' => $model->findAll(),
        ]);
    }

    public function create()
    {
        $model = new IpWhitelistModel();
        $data  = [
            'ip_address' => trim($this->request->getPost('ip_address')),
            'label'      => $this->request->getPost('label'),
            'is_active'  => 1,
        ];

        if ($model->save($data)) {
            return redirect()->to('/admin/ip-whitelist')
                ->with('success', 'آدرس IP با موفقیت اضافه شد.');
        }

        return redirect()->back()
            ->with('error', 'خطا: ' . implode(', ', $model->errors()))
            ->withInput();
    }

    public function delete($id)
    {
        $model = new IpWhitelistModel();
        $model->delete($id);
        return redirect()->to('/admin/ip-whitelist')
            ->with('success', 'آدرس IP حذف شد.');
    }
}
