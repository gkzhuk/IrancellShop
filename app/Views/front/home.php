<?= $this->extend('front/layout') ?>

<?= $this->section('content') ?>
<section class="hero">
    <div class="container position-relative">
        <div class="row align-items-center justify-content-center g-4">
            <div class="col-lg-7 text-lg-end text-center">
                <span class="badge rounded-pill text-bg-dark px-3 py-2 mb-3">نمایندگی فروش سیم‌کارت ایرانسل</span>
                <h1 class="display-5 fw-bold mb-3 lh-base">استعلام آنلاین و خرید آسان سیم‌کارت ایرانسل</h1>
                <p class="lead mb-0 text-dark opacity-75">شماره دلخواه خود را وارد کنید تا موجودی آن به‌صورت آنلاین بررسی شود.</p>
            </div>
            <div class="col-md-8 col-lg-5">
                <div class="soft-card border-0 p-3 p-md-4">
                    <div class="card-body p-0">
                        <div class="d-flex align-items-center justify-content-center justify-content-lg-start gap-2 mb-3">
                            <span class="icon-badge"><i class="bi bi-phone fs-3"></i></span>
                            <div class="text-end">
                                <h2 class="h5 fw-bold mb-1">بررسی شماره</h2>
                                <p class="small text-muted mb-0">فرمت شماره باید با ۰۹ شروع شود.</p>
                            </div>
                        </div>
                        <label class="form-label fw-bold" for="mobileInput">شماره مورد نظر خود را وارد کنید</label>
                        <div class="input-group input-group-lg input-group-responsive mb-3" dir="ltr">
                            <input type="text" id="mobileInput" class="form-control text-center fs-5" placeholder="09xxxxxxxxx" maxlength="11">
                            <span class="input-group-text bg-light">MTN Irancell</span>
                        </div>
                        <button id="checkBtn" class="btn btn-dark w-100 py-3 fs-5 rounded-3">بررسی شماره</button>
                        <p class="small text-muted text-center mb-0 mt-3">پس از تأیید موجودی، ادامه فرآیند خرید بدون تغییر انجام می‌شود.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-pad bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title h3 mb-3">خدمات فروش و پشتیبانی</h2>
            <p class="section-subtitle mb-0">ساختار ساده و شفاف برای انتخاب شماره، رزرو اولیه و پیگیری مراحل تکمیل خرید.</p>
        </div>
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="soft-card p-4 h-100">
                    <span class="icon-badge mb-3"><i class="bi bi-truck fs-3"></i></span>
                    <h3 class="h5 fw-bold mb-3">ارسال سریع</h3>
                    <p class="text-muted mb-0 lh-lg">تحویل در کمترین زمان ممکن پس از نهایی شدن مراحل ثبت و مالکیت سیم‌کارت.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="soft-card p-4 h-100">
                    <span class="icon-badge mb-3"><i class="bi bi-shield-check fs-3"></i></span>
                    <h3 class="h5 fw-bold mb-3">خرید امن</h3>
                    <p class="text-muted mb-0 lh-lg">استفاده از درگاه پرداخت معتبر و نمایش نماد اعتماد برای اطمینان بیشتر کاربران.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="soft-card p-4 h-100">
                    <span class="icon-badge mb-3"><i class="bi bi-headset fs-3"></i></span>
                    <h3 class="h5 fw-bold mb-3">پشتیبانی</h3>
                    <p class="text-muted mb-0 lh-lg">پاسخگویی و راهنمایی در مراحل پیش‌خرید، تأیید اطلاعات و تکمیل سفارش.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="about" class="section-pad">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <span class="badge rounded-pill bg-warning text-dark px-3 py-2 mb-3">درباره ما</span>
                <h2 class="section-title h3 mb-3">نمایندگی ایرانسل اسماعیلی بیگی</h2>
                <p class="section-subtitle mx-0 mb-4">نمایندگی ایرانسل اسماعیلی بیگی، به عنوان یکی از نمایندگان برتر در شهر کرمان در حوزه فروش محصولات ارتباطی از جمله مودم و سیم‌کارت، در راستای ارتقای کیفیت ارتباطی و خدمات فروش فعالیت می‌کند.</p>
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-dark rounded-pill px-4" type="button" data-bs-toggle="modal" data-bs-target="#contactModal">اطلاعات تماس</button>
                    <button class="btn btn-outline-dark rounded-pill px-4" type="button" data-bs-toggle="modal" data-bs-target="#rulesModal">قوانین و مقررات</button>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="soft-card p-4 h-100">
                            <span class="icon-badge mb-3"><i class="bi bi-telephone fs-3"></i></span>
                            <h3 class="h6 fw-bold mb-2">تلفن پشتیبانی</h3>
                            <a class="text-dark fw-bold text-decoration-none" href="tel:+989004449404">09004449404</a>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="soft-card p-4 h-100">
                            <span class="icon-badge mb-3"><i class="bi bi-geo-alt fs-3"></i></span>
                            <h3 class="h6 fw-bold mb-2">مراجعه حضوری</h3>
                            <p class="text-muted small lh-lg mb-0">کرمان، بلوار ۲۲ بهمن، خیابان چهارراه نیمه شعبان، بین کوچه ۱ و ۳</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-pad bg-white border-top">
    <div class="container">
        <div class="soft-card p-4 p-md-5">
            <div class="row align-items-center g-3">
                <div class="col-lg-7 text-center text-lg-end">
                    <h2 class="h4 fw-bold mb-2">دسترسی سریع</h2>
                    <p class="text-muted mb-lg-0 lh-lg">برای مطالعه قوانین، مشاهده اطلاعات نمایندگی یا تماس با پشتیبانی از لینک‌های زیر استفاده کنید.</p>
                </div>
                <div class="col-lg-5">
                    <div class="d-grid d-sm-flex gap-2 justify-content-lg-end">
                        <a class="btn btn-outline-dark rounded-pill px-4" href="#about">درباره ما</a>
                        <button class="btn btn-warning rounded-pill px-4" type="button" data-bs-toggle="modal" data-bs-target="#rulesModal">قوانین و مقررات</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#checkBtn').click(function() {
            var mobile = $('#mobileInput').val().trim();
            
            // Basic validation
            if (!/^09\d{9}$/.test(mobile)) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطا',
                    text: 'لطفا یک شماره معتبر ۱۱ رقمی وارد کنید که با ۰۹ شروع شود.',
                    confirmButtonText: 'باشه'
                });
                return;
            }

            // AJAX Check
            var btn = $(this);
            btn.prop('disabled', true).text('در حال بررسی...');
            
            $.ajax({
                url: '<?= base_url("AjaxSearchNumber") ?>',
                type: 'GET',
                data: { mobile: mobile },
                success: function(response) {
                    if (response.found) {
                        window.location.href = '<?= base_url("i/") ?>' + mobile;
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'موجود نیست',
                            text: 'متاسفانه این شماره موجود نیست یا قبلا فروخته شده است.',
                            confirmButtonText: 'باشه'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطا',
                        text: 'خطا در برقراری ارتباط با سرور.',
                        confirmButtonText: 'باشه'
                    });
                },
                complete: function() {
                    btn.prop('disabled', false).text('بررسی شماره');
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
