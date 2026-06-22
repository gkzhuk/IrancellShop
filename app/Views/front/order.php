<?= $this->extend('front/layout') ?>

<?= $this->section('content') ?>
<?php
    $displayPrice = (float) ($simcard['price'] ?? 0);
    $originalPrice = (float) ($simcard['original_price'] ?? $displayPrice);
    $finalPrice = (float) ($simcard['final_price'] ?? $displayPrice);
    $discountPercent = (float) ($simcard['discount_percent'] ?? 0);
    $discountEnabled = !empty($simcard['discount_enabled']);
    $hasDiscount = $discountEnabled && $discountPercent > 0;
    $payablePrice = $hasDiscount ? $finalPrice : $displayPrice;
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="checkout-card">
                <!-- Yellow Header -->
                <div class="checkout-header">
                    <h5 class="mb-3">شما درخواست خرید شماره زیر را دارید</h5>
                    <h2 class="sim-number" dir="ltr"><?= $simcard['number'] ?></h2>
                </div>

                <!-- Gift Boxes Section (Light Theme) -->
                <div class="gift-section px-4 mt-4" dir="rtl">
                    <div class="row g-2">
                        <!-- Box 1: Internet -->
                        <div class="col-4">
                            <div class="gift-box">
                                <div class="icon-wrapper">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 12.55a11 11 0 0 1 14.08 0"></path><path d="M1.42 9a16 16 0 0 1 21.16 0"></path><path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path><line x1="12" y1="20" x2="12.01" y2="20"></line>
                                    </svg>
                                </div>
                                <div class="gift-title">۹۰ گیگ اینترنت</div>
                                <div class="gift-subtitle">۳ ماهه</div>
                                <span class="badge-gift">هدیه</span>
                            </div>
                        </div>

                        <!-- Box 2: Calls -->
                        <div class="col-4">
                            <div class="gift-box">
                                <div class="icon-wrapper">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                    </svg>
                                </div>
                                <div class="gift-title">۲۷۰۰ دقیقه</div>
                                <div class="gift-subtitle">مکالمه</div>
                                <span class="badge-gift">هدیه</span>
                            </div>
                        </div>

                        <!-- Box 3: SMS -->
                        <div class="col-4">
                            <div class="gift-box">
                                <div class="icon-wrapper">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                    </svg>
                                </div>
                                <div class="gift-title">۲۷۰۰ پیامک</div>
                                <div class="gift-subtitle">رایگان</div>
                                <span class="badge-gift">هدیه</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Price Box -->
                <div class="price-section">
                    <div class="price-row align-items-start">
                        <span class="label">قیمت سیم‌کارت</span>
                        <span class="value text-end">
                            <?php if($hasDiscount): ?>
                                <span class="original-price d-block"><?= number_format($originalPrice / 10) ?> تومان</span>
                                <span class="discounted-price d-block"><?= number_format($payablePrice / 10) ?> تومان</span>
                                <span class="discount-badge d-inline-block mt-1">🔻 <?= rtrim(rtrim(number_format($discountPercent, 2), '0'), '.') ?>٪ تخفیف</span>
                            <?php else: ?>
                                <?= number_format($payablePrice / 10) ?> تومان
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="price-row">
                        <span class="label">هزینه ارسال</span>
                        <span class="value">رایگان</span>
                    </div>
                    <div class="divider"></div>
                    <div class="price-row total">
                        <span class="label">مبلغ قابل پرداخت</span>
                        <span class="value"><?= number_format($payablePrice / 10) ?> تومان</span>
                    </div>
                </div>

                <!-- Info Section -->
                <div class="info-section">
                    <div class="info-row">
                        <span class="info-label">نوع سیم‌کارت</span>
                        <span class="info-value">ایرانسل 0900</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">نوع شماره</span>
                        <span class="info-value">دائمی</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">اپراتور</span>
                        <span class="info-value">ایرانسل</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">زمان تحویل</span>
                        <span class="info-value">۷-۱۰ روز کاری</span>
                    </div>
                </div>

                <!-- Purchase Button -->
                <div class="text-center mt-4 pb-4">
                    <button type="button" class="btn-purchase" data-bs-toggle="modal" data-bs-target="#purchaseModal">
                        اطلاعات خریدار
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Purchase Modal -->
<div class="modal fade" id="purchaseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">تکمیل اطلاعات خرید</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?php if(session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                        <?php foreach(session()->getFlashdata('errors') as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <?php if(session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <form action="<?= base_url('payment/start') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="simcard_id" value="<?= $simcard['id'] ?>">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">نام</label>
                            <input type="text" class="form-control" name="buyer_first_name" value="<?= old('buyer_first_name') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">نام خانوادگی</label>
                            <input type="text" class="form-control" name="buyer_last_name" value="<?= old('buyer_last_name') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">کد ملی</label>
                            <input type="text" class="form-control" name="buyer_national_code" maxlength="10" value="<?= old('buyer_national_code') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">شماره موبایل (شماره موبایل باید به نام متقاضی باشد)</label>
                            <input type="text" class="form-control" name="buyer_phone" maxlength="11" placeholder="09xxxxxxxxx" value="<?= old('buyer_phone') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">نام پدر</label>
                            <input type="text" class="form-control" name="father_name" value="<?= old('father_name') ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">تاریخ تولد</label>
                            <div class="row g-2">
                                <div class="col">
                                    <select class="form-select" name="birth_day" required>
                                        <option value="">روز</option>
                                        <?php for($i=1; $i<=31; $i++) echo "<option value='$i'>$i</option>"; ?>
                                    </select>
                                </div>
                                <div class="col">
                                    <select class="form-select" name="birth_month" required>
                                        <option value="">ماه</option>
                                        <?php 
                                        $months = ['فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند'];
                                        foreach($months as $k => $m) echo "<option value='" . ($k+1) . "'>$m</option>"; 
                                        ?>
                                    </select>
                                </div>
                                <div class="col">
                                    <select class="form-select" name="birth_year" required>
                                        <option value="">سال</option>
                                        <?php for($i=1300; $i<=1400; $i++) echo "<option value='$i'>$i</option>"; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 mt-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="rules" id="rulesCheck" required>
                                <label class="form-check-label" for="rulesCheck">
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#rulesModal">قوانین و مقررات</a> را مطالعه کرده و می‌پذیرم.
                                </label>
                            </div>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-success w-100 py-3 fw-bold">تایید و پرداخت آنلاین</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Existing Checkout Card Styling */
.checkout-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}
.checkout-header {
    background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
    padding: 30px;
    text-align: center;
    color: #333;
}
.checkout-header h5 {
    font-size: 16px;
    font-weight: 500;
    margin-bottom: 15px;
}
.sim-number {
    font-size: 32px;
    font-weight: 700;
    letter-spacing: 2px;
    color: #000;
    margin: 0;
}

/* --- LIGHT THEME CSS for Gift Boxes --- */
.gift-box {
    background-color: #ffffff; /* Changed to white */
    border: 1px solid #e0e0e0; /* Changed to light gray border */
    border-radius: 12px;
    padding: 15px 10px;
    text-align: center;
    position: relative;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    cursor: default;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02); /* Subtle shadow for depth */
}
.gift-box:hover {
    border-color: #FFC107;
    box-shadow: 0 4px 15px rgba(255, 193, 7, 0.15); /* Yellow glow on hover */
}
.icon-wrapper {
    background-color: #FFFBF0; /* Very light yellow background */
    border: 1px solid rgba(255, 193, 7, 0.3);
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
}
.icon-wrapper svg {
    color: #f39c12; /* Darker gold/yellow for contrast on light background */
}
.gift-title {
    color: #333333; /* Dark gray for visibility */
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 4px;
}
.gift-subtitle {
    color: #777777; /* Medium gray */
    font-size: 11px;
    margin-bottom: 15px; 
}
.badge-gift {
    background-color: rgba(76, 175, 80, 0.1);
    color: #4CAF50;
    font-size: 10px;
    padding: 2px 8px;
    border-radius: 12px;
    position: absolute;
    bottom: 8px;
    right: 8px;
    font-weight: 600;
    border: 1px solid rgba(76, 175, 80, 0.2);
}

/* Original Price Section Styling */
.price-section {
    background: #FFFBF0;
    border: 2px solid #FFD700;
    border-radius: 12px;
    padding: 20px;
    margin: 20px;
}
.price-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    font-size: 15px;
}
.price-row .label {
    color: #666;
}
.price-row .value {
    font-weight: 600;
    color: #333;
}
.price-row.total {
    font-size: 18px;
}
.price-row.total .label,
.price-row.total .value {
    font-weight: 700;
    color: #000;
}
.original-price {
    color: #888;
    font-size: 14px;
    font-weight: 500;
    text-decoration: line-through;
}
.discounted-price {
    color: #d32f2f;
    font-size: 18px;
    font-weight: 800;
}
.discount-badge {
    background: #ffe8e8;
    color: #c62828;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    padding: 3px 10px;
}
.divider {
    height: 1px;
    background: linear-gradient(to right, transparent, #FFD700, transparent);
    margin: 10px 0;
}
.info-section {
    padding: 20px 30px;
}
.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #f0f0f0;
}
.info-row:last-child {
    border-bottom: none;
}
.info-label {
    color: #666;
    font-size: 14px;
}
.info-value {
    color: #333;
    font-weight: 600;
    font-size: 14px;
}
.btn-purchase {
    background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
    color: white;
    border: none;
    padding: 15px 50px;
    border-radius: 50px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
}
.btn-purchase:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
}

@media (max-width: 576px) {
    .sim-number {
        font-size: 24px;
    }
    
    .price-section {
        margin: 15px;
        padding: 15px;
    }
    
    .info-section {
        padding: 15px 20px;
    }

    /* Mobile adjustments for the gift boxes */
    .gift-title {
        font-size: 11px;
    }
    .gift-subtitle {
        font-size: 10px;
    }
    .gift-box {
        padding: 12px 5px;
    }
    .badge-gift {
        font-size: 9px;
        right: 4px;
        bottom: 4px;
    }
    .icon-wrapper {
        width: 30px;
        height: 30px;
    }
    .icon-wrapper svg {
        width: 16px;
        height: 16px;
    }
}
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var timerElement = document.querySelector('[data-fake-countdown], [data-countdown], #fakeCountdown, #fakeCountdownTimer, #countdownTimer, .fake-countdown, .countdown-timer');
    if (!timerElement) {
        return;
    }

    var storageKey = 'irancell_sim_order_countdown_expire_at_<?= (int) ($simcard['id'] ?? 0) ?>';

    function parseCurrentDuration(text) {
        var normalized = (text || '').replace(/[۰-۹٠-٩]/g, function (digit) {
            return '۰۱۲۳۴۵۶۷۸۹٠١٢٣٤٥٦٧٨٩'.indexOf(digit) % 10;
        });
        var parts = normalized.match(/\d+/g);
        if (!parts || !parts.length) {
            return null;
        }
        parts = parts.map(function (part) { return parseInt(part, 10); });
        if (parts.length >= 3) {
            return ((parts[0] * 60 * 60) + (parts[1] * 60) + parts[2]) * 1000;
        }
        if (parts.length === 2) {
            return ((parts[0] * 60) + parts[1]) * 1000;
        }
        return parts[0] * 1000;
    }

    function generateDuration() {
        if (typeof window.generateFakeCountdownDuration === 'function') {
            return window.generateFakeCountdownDuration();
        }

        var existingDuration = parseCurrentDuration(timerElement.textContent);
        if (existingDuration && existingDuration > 0) {
            return existingDuration;
        }

        var minDuration = parseInt(timerElement.getAttribute('data-min-duration') || '300000', 10);
        var maxDuration = parseInt(timerElement.getAttribute('data-max-duration') || '900000', 10);
        return Math.floor(Math.random() * (maxDuration - minDuration + 1)) + minDuration;
    }

    function getExpireAt() {
        var storedExpireAt = parseInt(localStorage.getItem(storageKey) || '0', 10);
        if (storedExpireAt && storedExpireAt > Date.now()) {
            return storedExpireAt;
        }

        localStorage.removeItem(storageKey);
        var expireAt = Date.now() + generateDuration();
        localStorage.setItem(storageKey, String(expireAt));
        return expireAt;
    }

    function toPersianNumber(value) {
        return String(value).replace(/\d/g, function (digit) {
            return '۰۱۲۳۴۵۶۷۸۹'[digit];
        });
    }

    function render(remainingMs) {
        var totalSeconds = Math.max(0, Math.floor(remainingMs / 1000));
        var minutes = Math.floor(totalSeconds / 60);
        var seconds = totalSeconds % 60;
        timerElement.textContent = toPersianNumber(minutes) + ':' + toPersianNumber(String(seconds).padStart(2, '0'));
    }

    var expireAt = getExpireAt();
    window.setInterval(function () {
        var remainingMs = expireAt - Date.now();
        if (remainingMs <= 0) {
            localStorage.removeItem(storageKey);
            expireAt = Date.now() + generateDuration();
            localStorage.setItem(storageKey, String(expireAt));
            remainingMs = expireAt - Date.now();
        }
        render(remainingMs);
    }, 1000);

    render(expireAt - Date.now());
});
</script>
<?= $this->endSection() ?>
