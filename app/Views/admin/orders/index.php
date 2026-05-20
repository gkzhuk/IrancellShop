<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<h2 class="mb-4">مدیریت سفارشات</h2>

<div class="card mb-4">
    <div class="card-body">
        <form action="" method="get" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="جستجو (کد رهگیری، نام، موبایل، Authority، Ref ID...)" value="<?= esc($search) ?>">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">همه وضعیت‌های پرداخت</option>
                    <?php foreach ($paymentStatuses as $key => $label): ?>
                        <option value="<?= esc($key) ?>" <?= $status === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">فیلتر</button>
            </div>
        </form>
    </div>
</div>

<?php
$statusBadges = [
    'pending'           => 'bg-secondary',
    'gateway_requested' => 'bg-info text-dark',
    'canceled'          => 'bg-warning text-dark',
    'verify_pending'    => 'bg-warning text-dark',
    'success'           => 'bg-success',
    'verify_failed'     => 'bg-danger',
    'failed'            => 'bg-danger',
];
?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>کد رهگیری</th>
                        <th>شماره سیم‌کارت</th>
                        <th>خریدار</th>
                        <th>کد ملی</th>
                        <th>موبایل</th>
                        <th>مبلغ (تومان)</th>
                        <th>وضعیت</th>
                        <th>Authority</th>
                        <th>Ref ID</th>
                        <th>پیام پرداخت</th>
                        <th>تاریخ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($orders as $order): ?>
                        <?php
                            $paymentStatus = $order['payment_status'] ?? 'pending';
                            $badgeClass = $statusBadges[$paymentStatus] ?? 'bg-dark';
                            $label = $paymentStatuses[$paymentStatus] ?? 'نامشخص';
                        ?>
                        <tr>
                            <td><?= esc($order['tracking_code']) ?></td>
                            <td dir="ltr"><?= esc($order['simcard_number']) ?></td>
                            <td><?= esc($order['buyer_name']) ?></td>
                            <td><?= esc($order['buyer_national_code']) ?></td>
                            <td><?= esc($order['buyer_phone']) ?></td>
                            <td><?= number_format($order['amount'] / 10) ?></td>
                            <td><span class="badge <?= esc($badgeClass) ?>"><?= esc($label) ?></span></td>
                            <td dir="ltr" style="max-width: 180px; word-break: break-all;"><?= esc($order['authority'] ?? '-') ?></td>
                            <td dir="ltr"><?= esc($order['ref_id'] ?? '-') ?></td>
                            <td style="max-width: 260px; white-space: normal;"><?= esc($order['payment_message'] ?? '-') ?></td>
                            <td dir="ltr"><?= jdate('Y/m/d H:i', strtotime($order['created_at'])) ?></td>
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
<?= $this->endSection() ?>
