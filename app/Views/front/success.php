<?= $this->extend('front/layout') ?>

<?= $this->section('content') ?>
<div class="container py-5 text-center">
    <div class="card shadow-sm mx-auto" style="max-width: 500px;">
        <div class="card-body p-5">
            <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
            <h2 class="mt-4 text-success">پرداخت موفق</h2>
            <p class="lead mt-3">سفارش شما با موفقیت ثبت شد.</p>
            
            <div class="alert alert-light border mt-4">
                <h5>کد رهگیری:</h5>
                <h1 class="display-4 fw-bold text-dark"><?= $order['tracking_code'] ?></h1>
            </div>
            
            <p class="text-muted mt-3">برای تکمیل سفارش، لطفاً مدارک خود را تکمیل کنید.</p>
            <p class="small text-muted">شماره تراکنش: <?= $ref_id ?></p>
            
            <?php $secureToken = $order['secure_token'] ?? ''; ?>
            <?php if ($secureToken !== ''): ?>
                <a href="<?= base_url('order/complete/' . $secureToken) ?>" class="btn btn-primary mt-4">تکمیل اطلاعات و بارگذاری مدارک</a>
            <?php else: ?>
                <a href="<?= current_url() ?>" class="btn btn-primary mt-4">تکمیل اطلاعات و بارگذاری مدارک</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
