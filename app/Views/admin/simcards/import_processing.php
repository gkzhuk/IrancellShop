<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-body text-center py-5">
        <div class="spinner-border text-primary mb-3" role="status"></div>
        <h4 class="mb-3">ایمپورت اکسل در حال پردازش است...</h4>
        <p class="text-muted mb-4">
            پردازش به‌صورت خودکار و بخش‌بخش ادامه پیدا می‌کند. لطفاً این صفحه را نبندید.
        </p>
        <div class="row justify-content-center text-center">
            <div class="col-md-2 col-6 mb-3">
                <div class="fw-bold"><?= number_format((int) ($result['chunks'] ?? 0)) ?></div>
                <small class="text-muted">بخش پردازش‌شده</small>
            </div>
            <div class="col-md-2 col-6 mb-3">
                <div class="fw-bold text-success"><?= number_format((int) ($result['inserted'] ?? 0)) ?></div>
                <small class="text-muted">درج</small>
            </div>
            <div class="col-md-2 col-6 mb-3">
                <div class="fw-bold text-info"><?= number_format((int) ($result['updated'] ?? 0)) ?></div>
                <small class="text-muted">بروزرسانی</small>
            </div>
            <div class="col-md-2 col-6 mb-3">
                <div class="fw-bold text-danger"><?= number_format((int) ($result['failed'] ?? 0)) ?></div>
                <small class="text-muted">خطا</small>
            </div>
        </div>
        <a href="<?= esc($continueUrl, 'attr') ?>" class="btn btn-outline-primary">ادامه پردازش</a>
    </div>
</div>
<script>
    window.setTimeout(function () {
        window.location.href = <?= json_encode($continueUrl, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
    }, 1200);
</script>
<?= $this->endSection() ?>
