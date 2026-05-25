<?= $this->extend('front/layout') ?>

<?= $this->section('content') ?>
<div class="container py-5 text-center">
    <div class="card shadow-sm mx-auto" style="max-width: 560px;">
        <div class="card-body p-5">
            <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 4rem;"></i>
            <h2 class="mt-3">لینک تکمیل مدارک نامعتبر است</h2>
            <p class="text-muted mt-3 mb-4"><?= esc($message ?? 'این لینک معتبر نیست یا دیگر فعال نیست.') ?></p>
            <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary">بازگشت به صفحه اصلی</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
