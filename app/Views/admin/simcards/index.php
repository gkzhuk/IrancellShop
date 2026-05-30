<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<?php
    $importStatusLabels = [
        'pending' => 'در انتظار',
        'processing' => 'در حال پردازش',
        'completed' => 'تکمیل شده',
        'done' => 'تکمیل شده',
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
    <div class="card-body table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>فایل</th>
                    <th>وضعیت</th>
                    <th>پردازش‌شده</th>
                    <th>درج</th>
                    <th>بروزرسانی</th>
                    <th>خطا</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($importJobs as $job): ?>
                <tr>
                    <td><?= $job['id'] ?></td>
                    <td><?= esc($job['original_filename'] ?? basename($job['file_path'])) ?></td>
                    <td><?= esc($importStatusLabels[$job['status']] ?? $job['status']) ?></td>
                    <td><?= number_format((int) $job['processed_rows']) ?> / <?= number_format((int) $job['total_rows']) ?></td>
                    <td><?= number_format((int) $job['inserted_rows']) ?></td>
                    <td><?= number_format((int) $job['updated_rows']) ?></td>
                    <td><?= number_format((int) $job['failed_rows']) ?></td>
                    <td>
                        <?php if(in_array($job['status'], ['pending', 'processing'], true)): ?>
                        <form action="<?= base_url('admin/simcards/imports/' . $job['id'] . '/process') ?>" method="post" class="d-inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-outline-primary">ادامه خودکار پردازش</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php if(!empty($job['error_log'])): ?>
                <tr>
                    <td colspan="8"><small class="text-danger"><pre class="mb-0" style="white-space: pre-wrap; direction:ltr; text-align:left;"><?= esc($job['error_log']) ?></pre></small></td>
                </tr>
                <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
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
document.querySelectorAll('.jalali-datetime-input').forEach(function (input) {
    input.addEventListener('blur', function () {
        var value = input.value.trim().replace(/-/g, '/');
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
