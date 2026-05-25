<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<h2 class="mb-4">مدیریت سفارشات موفق</h2>
<table class="table table-striped"><thead><tr><th>کد رهگیری</th><th>شماره سیم‌کارت</th><th>نام</th><th>کد ملی</th><th>موبایل</th><th>مبلغ</th><th>وضعیت سفارش</th><th>تاریخ</th><th>عملیات</th></tr></thead><tbody>
<?php foreach($orders as $o): ?><tr><td><?= esc($o['tracking_code']) ?></td><td><?= esc($o['simcard_number']) ?></td><td><?= esc(trim((($o['buyer_first_name'] ?? '') . ' ' . ($o['buyer_last_name'] ?? '')) ?: $o['buyer_name'])) ?></td><td><?= esc($o['buyer_national_code']) ?></td><td><?= esc($o['buyer_phone']) ?></td><td><?= number_format($o['amount']/10) ?></td><td><?= esc($orderStatuses[$o['order_status']] ?? $o['order_status']) ?></td><td><?= jdate('Y/m/d H:i', strtotime($o['created_at'])) ?></td><td><a class="btn btn-sm btn-primary" href="<?= base_url('admin/successful-orders/'.$o['id']) ?>">جزئیات</a></td></tr><?php endforeach; ?>
</tbody></table>
<?= $pager->links() ?>
<?= $this->endSection() ?>
