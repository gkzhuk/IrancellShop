<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<?php
    $importStatusLabels = [
        'pending' => 'در انتظار پردازش',
        'processing' => 'در حال پردازش',
        'completed' => 'با موفقیت انجام شد',
        'done' => 'با موفقیت انجام شد',
        'failed' => 'ناموفق',
    ];
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>مدیریت سیم‌کارت‌ها</h2>
    <div>
        <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-file-earmark-excel"></i> ایمپورت اکسل
        </button>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSimcardModal">
            <i class="bi bi-plus-lg"></i> افزودن تکی
        </button>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="" method="get" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="جستجو شماره..." value="<?= $search ?>">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">همه وضعیت‌ها</option>
                    <option value="free" <?= $status == 'free' ? 'selected' : '' ?>>آزاد</option>
                    <option value="sold" <?= $status == 'sold' ? 'selected' : '' ?>>فروخته شده</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">فیلتر</button>
            </div>
        </form>
    </div>
</div>


<div class="card mb-4">
    <div class="card-header">تنظیمات تخفیف</div>
    <div class="card-body">
        <form action="<?= base_url('admin/simcards/discount-settings') ?>" method="post" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-3">
                <label class="form-label">وضعیت تخفیف‌ها</label>
                <select name="enable_discounts" class="form-select">
                    <option value="0" <?= empty($discountSettings['enable_discounts']) ? 'selected' : '' ?>>غیرفعال</option>
                    <option value="1" <?= !empty($discountSettings['enable_discounts']) ? 'selected' : '' ?>>فعال</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">شروع کمپین عمومی</label>
                <input type="text" name="global_discount_start" class="form-control jalali-datetime-input" dir="ltr" placeholder="مثال: 1403/03/10 09:00" value="<?= esc($discountSettingsJalali['global_discount_start'] ?? '', 'attr') ?>">
                <small class="text-muted">تاریخ را به شمسی و با قالب YYYY/MM/DD HH:mm وارد کنید.</small>
            </div>
            <div class="col-md-3">
                <label class="form-label">پایان کمپین عمومی</label>
                <input type="text" name="global_discount_end" class="form-control jalali-datetime-input" dir="ltr" placeholder="مثال: 1403/03/20 23:59" value="<?= esc($discountSettingsJalali['global_discount_end'] ?? '', 'attr') ?>">
                <small class="text-muted">تاریخ را به شمسی و با قالب YYYY/MM/DD HH:mm وارد کنید.</small>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">ذخیره تنظیمات</button>
            </div>
        </form>
    </div>
</div>

<?php if(!empty($importJobs)): ?>
<div class="card mb-4">
    <div class="card-header">آخرین Jobهای ایمپورت</div>
    <div class="card-body">
        <div class="accordion" id="importJobsAccordion">
            <?php foreach($importJobs as $job): ?>
                <?php
                    $jobStatus = $job['status'] ?? 'pending';
                    $jobTitle = $job['original_filename'] ?? basename($job['file_path'] ?? '');
                    $jobTitle = $jobTitle !== '' ? $jobTitle : ('Job #' . ($job['id'] ?? ''));
                    $collapseId = 'importJobCollapse' . (int) $job['id'];
                    $headingId = 'importJobHeading' . (int) $job['id'];
                ?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="<?= esc($headingId, 'attr') ?>">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?= esc($collapseId, 'attr') ?>" aria-expanded="false" aria-controls="<?= esc($collapseId, 'attr') ?>">
                            <div class="d-flex flex-column flex-md-row gap-2 gap-md-4 w-100 align-items-md-center">
                                <span class="fw-bold"><?= esc($jobTitle) ?></span>
                                <span class="badge bg-secondary align-self-start"><?= esc($importStatusLabels[$jobStatus] ?? $jobStatus) ?></span>
                                <small class="text-muted">تاریخ ایجاد: <?= esc($job['created_at'] ?? '-') ?></small>
                            </div>
                        </button>
                    </h2>
                    <div id="<?= esc($collapseId, 'attr') ?>" class="accordion-collapse collapse" aria-labelledby="<?= esc($headingId, 'attr') ?>" data-bs-parent="#importJobsAccordion">
                        <div class="accordion-body">
                            <?php if(in_array($jobStatus, ['completed', 'done'], true)): ?>
                                <p class="text-success fw-bold mb-3">این ایمپورت با موفقیت انجام شد</p>
                                <div class="row g-2 text-center">
                                    <div class="col-6 col-md-3"><div class="border rounded p-2"><small class="text-muted d-block">کل ردیف‌ها</small><strong><?= number_format((int) ($job['total_rows'] ?? 0)) ?></strong></div></div>
                                    <div class="col-6 col-md-3"><div class="border rounded p-2"><small class="text-muted d-block">درج‌شده</small><strong><?= number_format((int) ($job['inserted_rows'] ?? 0)) ?></strong></div></div>
                                    <div class="col-6 col-md-3"><div class="border rounded p-2"><small class="text-muted d-block">به‌روزرسانی‌شده</small><strong><?= number_format((int) ($job['updated_rows'] ?? 0)) ?></strong></div></div>
                                    <div class="col-6 col-md-3"><div class="border rounded p-2"><small class="text-muted d-block">خطادار</small><strong><?= number_format((int) ($job['failed_rows'] ?? 0)) ?></strong></div></div>
                                </div>
                            <?php elseif($jobStatus === 'failed'): ?>
                                <p class="text-danger fw-bold mb-3">این ایمپورت با خطا مواجه شد</p>
                                <?php if(!empty($job['error_log'])): ?>
                                    <div class="alert alert-danger mb-0">
                                        <div class="fw-bold mb-2">جزئیات خطا برای بررسی مدیر:</div>
                                        <pre class="mb-0" style="white-space: pre-wrap;"><?= esc($job['error_log']) ?></pre>
                                    </div>
                                <?php endif; ?>
                            <?php elseif($jobStatus === 'processing'): ?>
                                <p class="fw-bold mb-2">در حال پردازش فایل</p>
                                <div class="progress mb-2" style="height: 24px;">
                                    <?php
                                        $totalRows = (int) ($job['total_rows'] ?? 0);
                                        $processedRows = (int) ($job['processed_rows'] ?? 0);
                                        $progressPercent = $totalRows > 0 ? min(100, (int) floor(($processedRows / $totalRows) * 100)) : 0;
                                    ?>
                                    <div class="progress-bar" role="progressbar" style="width: <?= $progressPercent ?>%;" aria-valuenow="<?= $progressPercent ?>" aria-valuemin="0" aria-valuemax="100"><?= $progressPercent ?>%</div>
                                </div>
                                <small class="text-muted"><?= number_format($processedRows) ?> / <?= number_format($totalRows) ?> ردیف پردازش شده است.</small>
                            <?php else: ?>
                                <p class="text-muted fw-bold mb-0">در انتظار پردازش</p>
                            <?php endif; ?>

                            <?php if(in_array($jobStatus, ['pending', 'processing'], true)): ?>
                                <form action="<?= base_url('admin/simcards/imports/' . $job['id'] . '/process') ?>" method="post" class="mt-3">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-primary">ادامه خودکار پردازش</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>شماره</th>
                        <th>قیمت نهایی (ریال)</th>
                        <th>قیمت اصلی (ریال)</th>
                        <th>تخفیف</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($simcards as $sim): ?>
                    <tr>
                        <td><?= $sim['number'] ?></td>
                        <?php
                            $originalPrice = $sim['original_price'] ?? $sim['price'];
                            $discountPercent = (float) ($sim['discount_percent'] ?? 0);
                            $discountEndsAt = $sim['discount_ends_at'] ?? null;
                        ?>
                        <td><?= number_format($sim['price']) ?></td>
                        <td><?= number_format($originalPrice) ?></td>
                        <td>
                            <?php if($discountPercent > 0): ?>
                                <span class="badge bg-info"><?= rtrim(rtrim(number_format($discountPercent, 2), '0'), '.') ?>٪</span>
                                <?php if($discountEndsAt): ?>
                                    <small class="d-block text-muted">تا <?= esc($discountEndsAt) ?></small>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($sim['status'] == 'free'): ?>
                                <span class="badge bg-success">آزاد</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">فروخته شده</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editSimModal<?= $sim['id'] ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <?php if($sim['status'] == 'free'): ?>
                            <a href="<?= base_url('admin/simcards/delete/' . $sim['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('آیا از حذف این سیم‌کارت اطمینان دارید؟')">
                                <i class="bi bi-trash"></i>
                            </a>
                            <?php endif; ?>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editSimModal<?= $sim['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">ویرایش قیمت سیم‌کارت <?= $sim['number'] ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="<?= base_url('admin/simcards/update/' . $sim['id']) ?>" method="post">
                                            <?= csrf_field() ?>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">قیمت (ریال)</label>
                                                    <input type="number" class="form-control" name="price" value="<?= $sim['price'] ?>" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                                                <button type="submit" class="btn btn-primary">ذخیره</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            <?= $pager->links() ?>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addSimcardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">افزودن سیم‌کارت جدید</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/simcards/create') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">شماره (۱۱ رقم با ۰۹)</label>
                        <input type="text" class="form-control" name="number" placeholder="09xxxxxxxxx" required pattern="09\d{9}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">قیمت (ریال)</label>
                        <input type="number" class="form-control" name="price" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-primary">افزودن</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ایمپورت سیم‌کارت از اکسل</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/simcards/import') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="alert alert-info">
                        فایل اکسل می‌تواند ستون‌های زیر را داشته باشد:<br>
                        ستون اول: شماره (مثلا 9123456789)<br>
                        ستون دوم: قیمت اصلی به ریال<br>
                        ستون سوم: درصد تخفیف (اختیاری)<br>
                        ستون چهارم: تاریخ پایان تخفیف همان ردیف (اختیاری)<br>
                        شماره‌های تکراری دیگر حذف نمی‌شوند و اطلاعات موجود آن‌ها به‌روزرسانی می‌شود.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">فایل اکسل (xlsx, xls, csv)</label>
                        <input type="file" class="form-control" name="excel_file" accept=".xlsx, .xls, .csv" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">اندازه هر بخش پردازش</label>
                        <input type="number" class="form-control" name="chunk_size" value="300" readonly>
                        <small class="text-muted">پردازش خودکار با بخش‌های ثابت ۳۰۰ ردیفی انجام می‌شود.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-primary">آپلود و پردازش</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function normalizePersianDigits(value) {
    return value.replace(/[۰-۹٠-٩]/g, function (digit) {
        return '۰۱۲۳۴۵۶۷۸۹٠١٢٣٤٥٦٧٨٩'.indexOf(digit) % 10;
    });
}

document.querySelectorAll('.jalali-datetime-input').forEach(function (input) {
    input.addEventListener('blur', function () {
        var value = normalizePersianDigits(input.value.trim()).replace(/-/g, '/');
        if (value && !/^1[34]\d{2}\/\d{1,2}\/\d{1,2}(\s+\d{1,2}:\d{1,2})?$/.test(value)) {
            input.setCustomValidity('تاریخ را به شمسی و با قالب YYYY/MM/DD HH:mm وارد کنید.');
        } else {
            input.setCustomValidity('');
            input.value = value;
        }
    });
});
</script>

<!-- Import Results Modal (triggered via session flashdata if needed, but for simplicity we show alert) -->
<?php if(session()->getFlashdata('import_report')): ?>
<div class="modal fade show" id="importResultModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">گزارش ایمپورت</h5>
                <button type="button" class="btn-close" onclick="document.getElementById('importResultModal').remove()"></button>
            </div>
            <div class="modal-body">
                <?= session()->getFlashdata('import_report') ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('importResultModal').remove()">بستن</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
