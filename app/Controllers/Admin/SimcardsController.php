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

        $discountSettings = $settingModel->getDiscountSettings();

        $data = [
            'simcards'               => $model->paginate(20),
            'pager'                  => $model->pager,
            'status'                 => $status,
            'search'                 => $search,
            'importJobs'             => $jobModel->orderBy('id', 'DESC')->findAll(10),
            'discountSettings'       => $discountSettings,
            'discountSettingsJalali' => $this->formatDiscountSettingsForJalali($discountSettings),
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
            'chunk_size'        => 300,
        ]);

        log_message('info', 'Inventory import job created: ' . $jobId);

        return redirect()->to('/admin/simcards/imports/' . $jobId . '/process');
    }

    public function processImport($id)
    {
        $service = new InventoryImportService();
        $result = $service->processJobUntilPaused((int) $id, 20);
        $status = $result['status'] ?? 'processing';

        if (!empty($result['paused']) && in_array($status, ['pending', 'processing'], true)) {
            $continueUrl = base_url('admin/simcards/imports/' . (int) $id . '/process');

            return $this->response->setBody(view('admin/simcards/import_processing', [
                'continueUrl' => $continueUrl,
                'result'      => $result,
            ]));
        }

        $message = $status === 'completed'
            ? 'ایمپورت فایل اکسل با موفقیت تکمیل شد.'
            : 'پردازش ایمپورت متوقف شد. وضعیت: ' . $this->importStatusLabel($status);

        return redirect()->to('/admin/simcards')->with($status === 'failed' ? 'error' : 'success', $message);
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

        $value = trim(str_replace('T', ' ', $value));

        if (preg_match('/^(1[34]\d{2})[-\/](\d{1,2})[-\/](\d{1,2})(?:\s+(\d{1,2}):(\d{1,2}))?$/', $value, $matches)) {
            $jalaliMonth = (int) $matches[2];
            $jalaliDay = (int) $matches[3];
            $hour = isset($matches[4]) ? (int) $matches[4] : 0;
            $minute = isset($matches[5]) ? (int) $matches[5] : 0;

            if ($jalaliMonth < 1 || $jalaliMonth > 12 || $jalaliDay < 1 || $jalaliDay > 31 || $hour > 23 || $minute > 59) {
                return null;
            }

            [$gy, $gm, $gd] = $this->jalaliToGregorian((int) $matches[1], $jalaliMonth, $jalaliDay);

            return sprintf('%04d-%02d-%02d %02d:%02d:00', $gy, $gm, $gd, $hour, $minute);
        }

        $timestamp = strtotime($value);

        return $timestamp === false ? null : date('Y-m-d H:i:s', $timestamp);
    }

    private function formatDiscountSettingsForJalali(array $settings): array
    {
        return [
            'global_discount_start' => $this->formatDateTimeForJalaliInput($settings['global_discount_start'] ?? null),
            'global_discount_end'   => $this->formatDateTimeForJalaliInput($settings['global_discount_end'] ?? null),
        ];
    }

    private function formatDateTimeForJalaliInput(?string $value): string
    {
        if (!$value) {
            return '';
        }

        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return '';
        }

        [$jy, $jm, $jd] = $this->gregorianToJalali((int) date('Y', $timestamp), (int) date('n', $timestamp), (int) date('j', $timestamp));

        return sprintf('%04d/%02d/%02d %s', $jy, $jm, $jd, date('H:i', $timestamp));
    }

    private function importStatusLabel(string $status): string
    {
        return [
            'pending'    => 'در انتظار',
            'processing' => 'در حال پردازش',
            'completed'  => 'تکمیل شده',
            'done'       => 'تکمیل شده',
            'failed'     => 'ناموفق',
        ][$status] ?? $status;
    }

    private function jalaliToGregorian(int $jy, int $jm, int $jd): array
    {
        $jy += 1595;
        $days = -355668 + (365 * $jy) + intdiv($jy, 33) * 8 + intdiv(($jy % 33) + 3, 4) + $jd;
        $days += ($jm < 7) ? (($jm - 1) * 31) : ((($jm - 7) * 30) + 186);
        $gy = 400 * intdiv($days, 146097);
        $days %= 146097;

        if ($days > 36524) {
            $gy += 100 * intdiv(--$days, 36524);
            $days %= 36524;
            if ($days >= 365) {
                $days++;
            }
        }

        $gy += 4 * intdiv($days, 1461);
        $days %= 1461;

        if ($days > 365) {
            $gy += intdiv($days - 1, 365);
            $days = ($days - 1) % 365;
        }

        $gd = $days + 1;
        $months = [0, 31, (($gy % 4 === 0 && $gy % 100 !== 0) || ($gy % 400 === 0)) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        for ($gm = 1; $gm <= 12 && $gd > $months[$gm]; $gm++) {
            $gd -= $months[$gm];
        }

        return [$gy, $gm, $gd];
    }

    private function gregorianToJalali(int $gy, int $gm, int $gd): array
    {
        $days = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
        $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
        $totalDays = 355666 + (365 * $gy) + intdiv($gy2 + 3, 4) - intdiv($gy2 + 99, 100) + intdiv($gy2 + 399, 400) + $gd + $days[$gm - 1];
        $jy = -1595 + (33 * intdiv($totalDays, 12053));
        $totalDays %= 12053;
        $jy += 4 * intdiv($totalDays, 1461);
        $totalDays %= 1461;

        if ($totalDays > 365) {
            $jy += intdiv($totalDays - 1, 365);
            $totalDays = ($totalDays - 1) % 365;
        }

        if ($totalDays < 186) {
            $jm = 1 + intdiv($totalDays, 31);
            $jd = 1 + ($totalDays % 31);
        } else {
            $jm = 7 + intdiv($totalDays - 186, 30);
            $jd = 1 + (($totalDays - 186) % 30);
        }

        return [$jy, $jm, $jd];
    }

}
