<?= $this->extend('front/layout') ?>
<?= $this->section('content') ?>
<div class="container py-5">
  <div class="card">
    <div class="card-body">
      <h4 class="mb-4">تکمیل اطلاعات سفارش</h4>

      <form id="documentCompletionForm" action="<?= base_url('order/complete/' . $order['secure_token']) ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <label class="form-label">نام و نام خانوادگی</label>
        <input class="form-control mb-2" value="<?= esc(((trim((string) ($order['buyer_first_name'] ?? '')) !== '' || trim((string) ($order['buyer_last_name'] ?? '')) !== '') ? trim(((string) ($order['buyer_first_name'] ?? '')) . ' ' . ((string) ($order['buyer_last_name'] ?? ''))) : ((string) ($order['buyer_name'] ?? '')))) ?>" readonly>

        <label class="form-label">کد ملی</label>
        <input class="form-control mb-2" value="<?= esc($order['buyer_national_code']) ?>" readonly>

        <label class="form-label">تاریخ تولد</label>
        <input class="form-control mb-2" value="<?= esc($order['buyer_birthdate']) ?>" readonly>

        <label class="form-label">نام پدر</label>
        <input class="form-control mb-2" value="<?= esc($order['buyer_father_name']) ?>" readonly>

        <label class="form-label">شماره موبایل</label>
        <input class="form-control mb-3" value="<?= esc($order['buyer_phone']) ?>" readonly>

        <label for="address" class="form-label">آدرس کامل <span class="text-danger">*</span></label>
        <textarea id="address" name="address" class="form-control mb-1" required <?= !empty($readonly) ? 'readonly' : '' ?>><?= esc(old('address', $order['address'] ?? '')) ?></textarea>
        <div class="form-text mb-2">آدرس دقیق پستی شامل شهر، خیابان، پلاک و واحد را وارد کنید.</div>
        <?php if (session('errors.address')): ?><div class="text-danger small mb-2"><?= esc(session('errors.address')) ?></div><?php endif; ?>

        <label for="postal_code" class="form-label">کد پستی <span class="text-danger">*</span></label>
        <input id="postal_code" name="postal_code" class="form-control mb-1" required pattern="[0-9]{10}" value="<?= esc(old('postal_code', $order['postal_code'] ?? '')) ?>" <?= !empty($readonly) ? 'readonly' : '' ?>>
        <div class="form-text mb-2">کد پستی باید دقیقاً ۱۰ رقم باشد.</div>
        <?php if (session('errors.postal_code')): ?><div class="text-danger small mb-2"><?= esc(session('errors.postal_code')) ?></div><?php endif; ?>

        <?php if (empty($readonly)): ?>
          <label for="national_id_document" class="form-label">تصویر کارت ملی <span class="text-danger">*</span></label>
          <input type="file" id="national_id_document" name="national_id_document" class="form-control mb-1" required accept=".jpg,.jpeg,.png,.pdf">
          <div class="form-text mb-2">فرمت‌های مجاز: jpg / png / pdf (الزامی)</div>
          <?php if (session('errors.national_id_document')): ?><div class="text-danger small mb-2"><?= esc(session('errors.national_id_document')) ?></div><?php endif; ?>

          <label for="selfie_document" class="form-label">تصویر سلفی متقاضی <span class="text-danger">*</span></label>
          <input type="file" id="selfie_document" name="selfie_document" class="form-control mb-1" required accept=".jpg,.jpeg,.png,.pdf">
          <div class="form-text mb-3">فرمت‌های مجاز: jpg / png / pdf (الزامی)</div>
          <?php if (session('errors.selfie_document')): ?><div class="text-danger small mb-3"><?= esc(session('errors.selfie_document')) ?></div><?php endif; ?>

          <button class="btn btn-primary">ثبت اطلاعات</button>
        <?php else: ?>
          <div class="alert alert-info">این سفارش تکمیل شده و فقط قابل مشاهده است.</div>
        <?php endif; ?>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="documentSubmitSuccessModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">اطلاعات با موفقیت ثبت شد</h5>
      </div>
      <div class="modal-body">
        <p class="mb-2">اطلاعات شما با موفقیت ثبت و برای بررسی ارسال شد.</p>
        <p class="mb-0">پس از بررسی مدارک، نتیجه از طریق پیامک به شماره ثبت‌شده اطلاع‌رسانی خواهد شد.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="documentSubmitSuccessConfirm">متوجه شدم</button>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('documentCompletionForm');
    var successModalElement = document.getElementById('documentSubmitSuccessModal');
    var confirmButton = document.getElementById('documentSubmitSuccessConfirm');
    var homeUrl = '<?= base_url('/') ?>';

    if (!form || !successModalElement || !confirmButton || typeof bootstrap === 'undefined') {
        return;
    }

    var successModal = new bootstrap.Modal(successModalElement, {
        backdrop: 'static',
        keyboard: false
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        var submitButton = form.querySelector('[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
        }

        fetch(form.action, {
            method: form.method || 'POST',
            body: new FormData(form),
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(function (response) {
            if (response.redirected && response.url.replace(/\/$/, '') === homeUrl.replace(/\/$/, '')) {
                successModal.show();
                return;
            }

            return response.text().then(function (html) {
                document.open();
                document.write(html);
                document.close();
            });
        }).catch(function () {
            form.submit();
        }).finally(function () {
            if (submitButton) {
                submitButton.disabled = false;
            }
        });
    });

    confirmButton.addEventListener('click', function () {
        window.location.href = homeUrl;
    });
});
</script>
<?= $this->endSection() ?>
