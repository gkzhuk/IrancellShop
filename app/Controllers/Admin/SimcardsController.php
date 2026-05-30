<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ExcelImportJobModel;
use App\Models\InventorySettingModel;
use App\Models\SimcardModel;
use App\Services\InventoryImportService;

class SimcardsController extends BaseController
{
    public function index()
    {
        $model = new SimcardModel();
        
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('search');

        if ($status && in_array($status, ['free', 'sold'])) {
            $model->where('status', $status);
        }
        
        if ($search) {
            $model->like('number', $search);
        }

        $jobModel = new ExcelImportJobModel();
        $settingModel = new InventorySettingModel();

        $data = [
            'simcards'         => $model->paginate(20),
            'pager'            => $model->pager,
            'status'           => $status,
            'search'           => $search,
            'importJobs'       => $jobModel->orderBy('id', 'DESC')->findAll(10),
            'discountSettings' => $settingModel->getDiscountSettings(),
        ];

        return view('admin/simcards/index', $data);
    }

    public function create()
    {
        $model = new SimcardModel();
        $data = [
            'number' => $this->request->getPost('number'),
            'price'  => $this->request->getPost('price'),
            'status' => 'free',
        ];

        if ($model->save($data)) {
            return redirect()->to('/admin/simcards')->with('success', 'سیم‌کارت جدید با موفقیت اضافه شد.');
        } else {
            return redirect()->back()->with('error', 'خطا در افزودن سیم‌کارت.')->withInput();
        }
    }

    public function update($id)
    {
        $model = new SimcardModel();
        $data = [
            'id'    => $id,
            'price' => $this->request->getPost('price'),
        ];

        if ($model->save($data)) {
            return redirect()->to('/admin/simcards')->with('success', 'قیمت سیم‌کارت بروزرسانی شد.');
        } else {
            return redirect()->back()->with('error', 'خطا در ویرایش.')->withInput();
        }
    }

    public function delete($id)
    {
        $model = new SimcardModel();
        $simcard = $model->find($id);

        if ($simcard && $simcard['status'] == 'free') {
            $model->delete($id);
            return redirect()->to('/admin/simcards')->with('success', 'سیم‌کارت حذف شد.');
        } else {
            return redirect()->to('/admin/simcards')->with('error', 'فقط سیم‌کارت‌های آزاد قابل حذف هستند.');
        }
    }
    
    public function import()
    {
        $file = $this->request->getFile('excel_file');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'فایل نامعتبر است.');
        }

        $extension = strtolower($file->getClientExtension());
        if (!in_array($extension, ['xlsx', 'xls', 'csv'], true)) {
            return redirect()->back()->with('error', 'فقط فایل‌های xlsx، xls یا csv قابل قبول هستند.');
        }

        $uploadDir = WRITEPATH . 'uploads/imports';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $storedName = $file->getRandomName();
        $file->move($uploadDir, $storedName);

        $jobModel = new ExcelImportJobModel();
        $jobId = $jobModel->insert([
            'file_path'         => $uploadDir . DIRECTORY_SEPARATOR . $storedName,
            'original_filename' => $file->getClientName(),
            'status'            => 'pending',
            'current_row'       => 2,
            'chunk_size'        => (int) ($this->request->getPost('chunk_size') ?: 300),
        ]);

        log_message('info', 'Inventory import job created: ' . $jobId);

        return redirect()->to('/admin/simcards')->with(
            'success',
            'فایل با موفقیت ذخیره شد و job ایمپورت ایجاد شد. برای پردازش تدریجی، کران php spark imports:process را اجرا کنید.'
        );
    }

    public function processImport($id)
    {
        $service = new InventoryImportService();
        $result = $service->processChunk((int) $id);

        $message = 'یک بخش از فایل پردازش شد. وضعیت: ' . ($result['status'] ?? '-');

        return redirect()->to('/admin/simcards')->with('success', $message);
    }

    public function discountSettings()
    {
        $settingModel = new InventorySettingModel();

        $enableDiscounts = $this->request->getPost('enable_discounts') === '1' ? '1' : '0';
        $globalStart = $this->normalizeDateTimeInput($this->request->getPost('global_discount_start'));
        $globalEnd = $this->normalizeDateTimeInput($this->request->getPost('global_discount_end'));

        $settingModel->setValue('enable_discounts', $enableDiscounts);
        $settingModel->setValue('global_discount_start', $globalStart);
        $settingModel->setValue('global_discount_end', $globalEnd);

        return redirect()->to('/admin/simcards')->with('success', 'تنظیمات تخفیف ذخیره شد.');
    }

    private function normalizeDateTimeInput(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $timestamp = strtotime($value);

        return $timestamp === false ? null : date('Y-m-d H:i:s', $timestamp);
    }

}
