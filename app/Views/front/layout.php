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
        body { font-family: 'IRANSansXFaNum', sans-serif; background-color: #fcfcfc; }
        .bg-irancell { background-color: #FFD700; color: #333; }
        .btn-irancell { background-color: #FFC107; color: #000; border: none; }
        .btn-irancell:hover { background-color: #FFB300; }
        .hero { background-color: #FFD700; padding: 60px 0; text-align: center; margin-bottom: 40px; }
        footer { background-color: #333; color: #FFD700; padding: 20px 0; margin-top: 50px; }
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
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= base_url() ?>">
                <img src="<?= base_url('img/logo.png') ?>" alt="Logo" height="40" class="d-inline-block align-text-top me-2">
                نمایندگی ایرانسل - اسماعیل بیگی
            </a>
            <div class="mr-auto">
                <button class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#contactModal">تماس با ما</button>
            </div>
        </div>
    </nav>

    <?= $this->renderSection('content') ?>

    <footer>
        <div class="container text-center">
            <p>&copy; 1404 تمامی حقوق محفوظ است.</p>
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
                <div class="modal-body">
                    <p><strong>تلفن:</strong> <a href="tel:+989004449404">09004449404</a></p>
                    <p><strong>آدرس:</strong> کرمان بلوار ۲۲ بهمن خیابان چهارراه نیمه شعبان بین کوچه ۱ و ۳ نمایندگی ایرانسل</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Rules Modal (Global placeholder) -->
    <div class="modal fade" id="rulesModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">قوانین و مقررات</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>کاربر گرامی، لطفاً پیش از ثبت سفارش این قوانین را با دقت مطالعه نمایید. ثبت پیش&zwnj;خرید به منزله پذیرش کامل قوانین است.<br />
ماهیت سفارش:<br />
تمامی سفارش&zwnj;های ثبت&zwnj;شده در این سایت در مرحله&zwnj;ی اول پیش&zwnj;خرید محسوب می&zwnj;شود. پرداخت اولیه تنها به&zwnj;عنوان رزرو شماره&zwnj;ی انتخابی شما است.<br />
<br />
تماس جهت تکمیل خرید:<br />
پس از ثبت پیش&zwnj;خرید، طی ۲۴ تا ۷۲ ساعت کاری کارشناسان ما با شما تماس گرفته و جهت تأیید نهایی اطلاعات، دریافت کد تأیید و تکمیل مراحل انتقال مالکیت و سند سیم&zwnj;کارت اقدام خواهد شد.<br />
<br />
احتمال فروش توسط سایر نمایندگی&zwnj;ها:<br />
ممکن است شماره سیم&zwnj;کارت به دلیل فروش هم&zwnj;زمان در نمایندگی&zwnj;های دیگر ایرانسل، پیش از تماس نهایی به فروش برسد. در این صورت سایت هیچ مسئولیتی ندارد و مبلغ پرداختی به&zwnj;طور کامل عودت داده می&zwnj;شود یا می&zwnj;توانید شماره دیگری انتخاب کنید.<br />
مدارک موردنیاز:<br />
ارائه مدارک هویتی مانند کارت ملی برای تکمیل فرآیند ثبت&zwnj;نام و انتقال مالکیت الزامی است. عدم همکاری در ارائه مدارک موجب لغو سفارش خواهد شد.<br />
<br />
تعهدات کاربر:<br />
کاربر موظف است هنگام ثبت اطلاعات، مشخصات صحیح و قابل تماس وارد کند. در صورت عدم پاسخگویی یا عدم امکان تکمیل فرآیند، سفارش لغو خواهد شد.<br />
<br />
<span class="text-danger">در صورتیکه لغو سفارش توسط کاربر صورت بگیرد، با احترام ۲۵ تا۳۰ درصد از مبلغ سفارش به عنوان جریمه و خسارت کسر میگردد.</span><br />
<br />
حریم خصوصی و امنیت:<br />
سایت موظف است اطلاعات شما را محرمانه نگه دارد و از آن تنها برای نهایی&zwnj;سازی خرید استفاده کند.<br />
<br />
با ثبت سفارش در سایت، شما اعلام می&zwnj;کنید که کلیه قوانین فوق را مطالعه کرده و به آن&zwnj;ها پایبند هستید.</p>

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
