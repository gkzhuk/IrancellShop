<?= $this->extend('front/layout') ?>

<?= $this->section('content') ?>
<section class="section-pad">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-8">
                <span class="badge rounded-pill bg-warning text-dark px-3 py-2 mb-3">درباره ما</span>
                <h1 class="section-title display-6 mb-3">نمایندگی ایرانسل اسماعیلی بیگی</h1>
                <p class="section-subtitle mb-0">نمایندگی ایرانسل اسماعیلی بیگی، به عنوان یکی از نمایندگان برتر در شهر کرمان در حوزه فروش محصولات ارتباطی از جمله مودم و سیم‌کارت، در راستای ارتقای کیفیت ارتباطی و خدمات فروش فعالیت می‌کند.</p>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="soft-card p-4 h-100 text-center">
                    <span class="icon-badge mb-3"><i class="bi bi-sim fs-3"></i></span>
                    <h2 class="h5 fw-bold mb-3">فروش سیم‌کارت</h2>
                    <p class="text-muted lh-lg mb-0">ارائه شماره‌های ایرانسل و راهنمایی برای تکمیل فرآیند خرید و انتقال مالکیت.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="soft-card p-4 h-100 text-center">
                    <span class="icon-badge mb-3"><i class="bi bi-router fs-3"></i></span>
                    <h2 class="h5 fw-bold mb-3">محصولات ارتباطی</h2>
                    <p class="text-muted lh-lg mb-0">فعالیت در حوزه فروش محصولات ارتباطی از جمله مودم و خدمات مرتبط با نیاز کاربران.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="soft-card p-4 h-100 text-center">
                    <span class="icon-badge mb-3"><i class="bi bi-patch-check fs-3"></i></span>
                    <h2 class="h5 fw-bold mb-3">خدمات فروش</h2>
                    <p class="text-muted lh-lg mb-0">تمرکز بر ارتقای کیفیت ارتباطی، شفافیت مراحل سفارش و پشتیبانی کاربران.</p>
                </div>
            </div>
        </div>

        <div class="soft-card p-4 p-md-5">
            <div class="text-center mb-4">
                <h2 class="h3 fw-bold mb-3">اطلاعات پشتیبانی</h2>
                <p class="text-muted mb-0">اطلاعات زیر از بخش تماس با ما در صفحه اصلی استخراج شده است.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="border rounded-4 p-4 h-100 bg-white">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <span class="icon-badge"><i class="bi bi-telephone fs-3"></i></span>
                            <h3 class="h5 fw-bold mb-0">تلفن پشتیبانی</h3>
                        </div>
                        <a class="fs-5 fw-bold text-dark text-decoration-none" href="tel:+989004449404">09004449404</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded-4 p-4 h-100 bg-white">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <span class="icon-badge"><i class="bi bi-geo-alt fs-3"></i></span>
                            <h3 class="h5 fw-bold mb-0">آدرس پشتیبانی حضوری</h3>
                        </div>
                        <p class="text-muted lh-lg mb-0">کرمان بلوار ۲۲ بهمن خیابان چهارراه نیمه شعبان بین کوچه ۱ و ۳ نمایندگی ایرانسل</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
