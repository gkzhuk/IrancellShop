<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<h2>جزئیات سفارش <?= esc($order['tracking_code']) ?></h2>
<ul>
<li>نام: <?= esc($order['buyer_name']) ?></li><li>کد ملی: <?= esc($order['buyer_national_code']) ?></li><li>تاریخ تولد: <?= esc($order['buyer_birthdate']) ?></li><li>نام پدر: <?= esc($order['buyer_father_name']) ?></li><li>موبایل: <?= esc($order['buyer_phone']) ?></li><li>آدرس: <?= esc($order['address'] ?? '-') ?></li><li>کدپستی: <?= esc($order['postal_code'] ?? '-') ?></li>
<li>Authority: <?= esc($order['authority']) ?></li><li>Ref ID: <?= esc($order['ref_id']) ?></li><li>مبلغ: <?= number_format($order['amount']/10) ?></li><li>تاریخ پرداخت: <?= esc($order['payment_verified_at'] ?? '-') ?></li>
</ul>
<form method="post" action="<?= base_url('admin/successful-orders/'.$order['id'].'/status') ?>"><?= csrf_field() ?><select name="admin_status" class="form-select"><?php foreach($adminStatuses as $k=>$v): ?><option value="<?= esc($k) ?>" <?= ($order['admin_status']??'')===$k?'selected':'' ?>><?= esc($v) ?></option><?php endforeach; ?></select><button class="btn btn-success mt-2">ذخیره</button></form>
<?= $this->endSection() ?>
