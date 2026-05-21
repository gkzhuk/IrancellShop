<?= $this->extend('front/layout') ?>
<?= $this->section('content') ?>
<div class="container py-5"><div class="card"><div class="card-body">
<h4>تکمیل اطلاعات سفارش</h4>
<form action="<?= base_url('order/complete/'.$order['secure_token']) ?>" method="post" enctype="multipart/form-data"><?= csrf_field() ?>
<input class="form-control mb-2" value="<?= esc($order['buyer_name']) ?>" readonly>
<input class="form-control mb-2" value="<?= esc($order['buyer_national_code']) ?>" readonly>
<input class="form-control mb-2" value="<?= esc($order['buyer_birthdate']) ?>" readonly>
<input class="form-control mb-2" value="<?= esc($order['buyer_father_name']) ?>" readonly>
<input class="form-control mb-2" value="<?= esc($order['buyer_phone']) ?>" readonly>
<textarea name="address" class="form-control mb-2" required <?= !empty($readonly)?'readonly':'' ?>><?= old('address', $order['address'] ?? '') ?></textarea>
<input name="postal_code" class="form-control mb-2" required pattern="[0-9]{10}" value="<?= old('postal_code', $order['postal_code'] ?? '') ?>" <?= !empty($readonly)?'readonly':'' ?>>
<?php if(empty($readonly)): ?><input type="file" name="national_id_document" class="form-control mb-2" required><input type="file" name="selfie_document" class="form-control mb-2" required><button class="btn btn-primary">ثبت اطلاعات</button><?php else: ?><div class="alert alert-info">این سفارش تکمیل شده و فقط قابل مشاهده است.</div><?php endif; ?>
</form></div></div></div>
<?= $this->endSection() ?>
