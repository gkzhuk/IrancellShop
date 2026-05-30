<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'نمایندگی فروش سیم‌کارت ایرانسل' ?></title>
    <link href="https://gitcdn.ir/library/twitter-bootstrap/5.3.8/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://gitcdn.ir/library/PersianFonts/IranSans/7.8/iransans78.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://gitcdn.ir/library/bootstrap-icons/1.10.0/bootstrap-icons.css">
    <style>
        :root {
            --irancell-yellow: #FFD700;
            --irancell-yellow-dark: #FFC107;
            --irancell-black: #1f1f1f;
            --irancell-muted: #6c757d;
            --irancell-border: rgba(31, 31, 31, .1);
            --irancell-shadow: 0 1rem 2.5rem rgba(31, 31, 31, .08);
        }

        body {
            font-family: 'IRANSansXFaNum', sans-serif;
            background: linear-gradient(180deg, #fffdf1 0, #fcfcfc 320px);
            color: var(--irancell-black);
        }

        .bg-irancell { background-color: var(--irancell-yellow); color: #333; }
        .btn-irancell { background-color: var(--irancell-yellow-dark); color: #000; border: none; }
        .btn-irancell:hover { background-color: #FFB300; }
        .navbar { --bs-navbar-padding-y: .85rem; }
        .navbar-brand { white-space: normal; line-height: 1.7; }
        .navbar-actions { gap: .5rem; }
        .nav-link-pill {
            border: 1px solid rgba(0, 0, 0, .2);
            border-radius: 999px;
            color: #1f1f1f;
            padding: .45rem .9rem;
            text-decoration: none;
            transition: all .2s ease;
        }
        .nav-link-pill:hover,
        .nav-link-pill:focus {
            background: #1f1f1f;
            color: var(--irancell-yellow);
        }
        .hero {
            background:
                radial-gradient(circle at 10% 15%, rgba(255, 255, 255, .55), transparent 28%),
                linear-gradient(135deg, #ffe66d 0%, var(--irancell-yellow) 48%, #ffc107 100%);
            padding: clamp(3rem, 7vw, 6rem) 0;
            text-align: center;
            margin-bottom: 0;
            position: relative;
            overflow: hidden;
        }
        .hero::after {
            content: '';
            position: absolute;
            inset-inline-start: -5rem;
            bottom: -7rem;
            width: 16rem;
            height: 16rem;
            border-radius: 50%;
            background: rgba(31, 31, 31, .08);
        }
        .section-pad { padding: clamp(3rem, 6vw, 5rem) 0; }
        .section-title { font-weight: 800; line-height: 1.8; }
        .section-subtitle { color: var(--irancell-muted); line-height: 2; max-width: 720px; margin-inline: auto; }
        .soft-card {
            background: #fff;
            border: 1px solid var(--irancell-border);
            border-radius: 1.35rem;
            box-shadow: 0 .75rem 2rem rgba(31, 31, 31, .06);
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .soft-card:hover { transform: translateY(-3px); box-shadow: var(--irancell-shadow); }
        .icon-badge {
            width: 3.25rem;
            height: 3.25rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 1rem;
            background: rgba(255, 215, 0, .2);
            color: #a17600;
        }
        .footer-links a,
        .footer-links button { color: var(--irancell-yellow); text-decoration: none; }
        .footer-links a:hover,
        .footer-links button:hover { color: #fff; }
        footer { background-color: #333; color: var(--irancell-yellow); padding: 28px 0; margin-top: 0; }
        .modal-content { border: 0; border-radius: 1.25rem; overflow: hidden; }
        .modal-header { background: #1f1f1f; color: var(--irancell-yellow); border-bottom: 0; }
        .modal-header .btn-close { filter: invert(1) grayscale(100%) brightness(200%); opacity: .9; }
        .contact-list p { line-height: 2; margin-bottom: 1rem; }
        .rules-body {
            max-height: 80vh;
            overflow-y: auto;
            padding: 1.25rem;
            line-height: 2;
            background: #fffdfa;
        }
        .rules-intro {
            background: rgba(255, 215, 0, .16);
            border: 1px solid rgba(255, 193, 7, .35);
            border-radius: 1rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .rules-section {
            padding: 1.1rem 0;
            border-bottom: 1px solid rgba(31, 31, 31, .08);
        }
        .rules-section:last-child { border-bottom: 0; }
        .rules-section h6 {
            color: #9a7300;
            font-weight: 800;
            margin-bottom: .75rem;
        }
        .rules-section p,
        .rules-section li { color: #343a40; }
        .rules-section ul { margin-bottom: 0; padding-right: 1.25rem; }
        .rules-warning {
            background: rgba(220, 53, 69, .08);
            border-right: 4px solid #dc3545;
            border-radius: .75rem;
            color: #b02a37;
            padding: .85rem 1rem;
            font-weight: 700;
        }

        @media (max-width: 767.98px) {
            .navbar .container { gap: .75rem; }
            .navbar-actions { width: 100%; justify-content: center; flex-wrap: wrap; }
            .nav-link-pill,
            .navbar-actions .btn { flex: 1 1 auto; text-align: center; }
            .input-group-responsive { flex-direction: column; gap: .75rem; }
            .input-group-responsive > .form-control,
            .input-group-responsive > .input-group-text { width: 100%; border-radius: .75rem !important; }
            .rules-body { max-height: 72vh; padding: 1rem; }
        }
    </style>
    <!-- Matomo -->
<script>
  var _paq = window._paq = window._paq || [];
  /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="https://analytics.datees.net/";
    _paq.push(['setTrackerUrl', u+'matomo.php']);
    _paq.push(['setSiteId', '249']);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<!-- End Matomo Code -->

</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-irancell shadow-sm">
        <div class="container d-flex flex-wrap align-items-center justify-content-between">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="<?= base_url() ?>">
                <img src="<?= base_url('img/logo.png') ?>" alt="Logo" height="40" class="d-inline-block align-text-top ms-2">
                <span>نمایندگی ایرانسل - اسماعیل بیگی</span>
            </a>
            <div class="navbar-actions d-flex align-items-center">
                <a class="nav-link-pill" href="<?= base_url('#about') ?>">درباره ما</a>
                <button class="nav-link-pill bg-transparent" type="button" data-bs-toggle="modal" data-bs-target="#rulesModal">قوانین و مقررات</button>
                <button class="btn btn-outline-dark btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#contactModal">تماس با ما</button>
            </div>
        </div>
    </nav>

    <?= $this->renderSection('content') ?>

    <footer>
        <div class="container text-center">
            <div class="footer-links d-flex flex-wrap justify-content-center gap-3 mb-3">
                <a href="<?= base_url('#about') ?>">درباره ما</a>
                <button class="btn btn-link p-0 text-decoration-none" type="button" data-bs-toggle="modal" data-bs-target="#rulesModal">قوانین و مقررات</button>
                <button class="btn btn-link p-0 text-decoration-none" type="button" data-bs-toggle="modal" data-bs-target="#contactModal">تماس با ما</button>
            </div>
            <p class="mb-0">&copy; 1404 تمامی حقوق محفوظ است.</p>
            <div class="mt-3">
              <a referrerpolicy='origin' target='_blank' href='https://trustseal.enamad.ir/?id=706947&Code=q1fcBPIAgzTceIpfInEWDE45NiuuJQBN'><img referrerpolicy='origin' src='https://trustseal.enamad.ir/logo.aspx?id=706947&Code=q1fcBPIAgzTceIpfInEWDE45NiuuJQBN' alt='' style='cursor:pointer' code='q1fcBPIAgzTceIpfInEWDE45NiuuJQBN'></a>
            </div>
        </div>
    </footer>

    <!-- Contact Modal -->
    <div class="modal fade" id="contactModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">اطلاعات تماس</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body contact-list">
                    <p><strong>تلفن:</strong> <a href="tel:+989004449404">09004449404</a></p>
                    <p><strong>آدرس:</strong> کرمان بلوار ۲۲ بهمن خیابان چهارراه نیمه شعبان بین کوچه ۱ و ۳ نمایندگی ایرانسل</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Rules Modal (Global placeholder) -->
    <div class="modal fade" id="rulesModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">قوانین و مقررات</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body rules-body">
                    <div class="rules-intro">
                        کاربر گرامی، لطفاً پیش از ثبت سفارش این قوانین را با دقت مطالعه نمایید. ثبت پیش‌خرید به منزله پذیرش کامل قوانین است.
                    </div>

                    <section class="rules-section">
                        <h6>۱. ماهیت سفارش</h6>
                        <p class="mb-0">تمامی سفارش‌های ثبت‌شده در این سایت در مرحله‌ی اول پیش‌خرید محسوب می‌شود. پرداخت اولیه تنها به‌عنوان رزرو شماره‌ی انتخابی شما است.</p>
                    </section>

                    <section class="rules-section">
                        <h6>۲. تماس جهت تکمیل خرید</h6>
                        <p class="mb-0">پس از ثبت پیش‌خرید، طی ۲۴ تا ۷۲ ساعت کاری کارشناسان ما با شما تماس گرفته و جهت تأیید نهایی اطلاعات، دریافت کد تأیید و تکمیل مراحل انتقال مالکیت و سند سیم‌کارت اقدام خواهد شد.</p>
                    </section>

                    <section class="rules-section">
                        <h6>۳. احتمال فروش توسط سایر نمایندگی‌ها</h6>
                        <p class="mb-0">ممکن است شماره سیم‌کارت به دلیل فروش هم‌زمان در نمایندگی‌های دیگر ایرانسل، پیش از تماس نهایی به فروش برسد. در این صورت سایت هیچ مسئولیتی ندارد و مبلغ پرداختی به‌طور کامل عودت داده می‌شود یا می‌توانید شماره دیگری انتخاب کنید.</p>
                    </section>

                    <section class="rules-section">
                        <h6>۴. مدارک موردنیاز</h6>
                        <ul>
                            <li>ارائه مدارک هویتی مانند کارت ملی برای تکمیل فرآیند ثبت‌نام و انتقال مالکیت الزامی است.</li>
                            <li>عدم همکاری در ارائه مدارک موجب لغو سفارش خواهد شد.</li>
                        </ul>
                    </section>

                    <section class="rules-section">
                        <h6>۵. تعهدات کاربر</h6>
                        <p>کاربر موظف است هنگام ثبت اطلاعات، مشخصات صحیح و قابل تماس وارد کند. در صورت عدم پاسخگویی یا عدم امکان تکمیل فرآیند، سفارش لغو خواهد شد.</p>
                        <div class="rules-warning">در صورتیکه لغو سفارش توسط کاربر صورت بگیرد، با احترام ۲۵ تا۳۰ درصد از مبلغ سفارش به عنوان جریمه و خسارت کسر میگردد.</div>
                    </section>

                    <section class="rules-section">
                        <h6>۶. حریم خصوصی و امنیت</h6>
                        <p class="mb-0">سایت موظف است اطلاعات شما را محرمانه نگه دارد و از آن تنها برای نهایی‌سازی خرید استفاده کند.</p>
                    </section>

                    <section class="rules-section">
                        <h6>۷. پذیرش نهایی</h6>
                        <p class="mb-0">با ثبت سفارش در سایت، شما اعلام می‌کنید که کلیه قوانین فوق را مطالعه کرده و به آن‌ها پایبند هستید.</p>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <script src="https://gitcdn.ir/library/twitter-bootstrap/5.3.8/bootstrap.bundle.min.js"></script>
    <script src="https://gitcdn.ir/library/sweetalert2/9/sweetalert2.min.js"></script>
    <script src="https://gitcdn.ir/library/jquery/3.6.0/jquery-3.6.0.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
