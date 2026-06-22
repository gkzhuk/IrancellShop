(function () {
    'use strict';

    function onReady(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
            return;
        }

        callback();
    }

    onReady(function () {
        var timerElement = document.getElementById('offerCountdown');
        if (!timerElement) {
            return;
        }

        var simcardIdElement = document.querySelector('[data-simcard-id], input[name="simcard_id"]');
        var simcardId = simcardIdElement ? (simcardIdElement.getAttribute('data-simcard-id') || simcardIdElement.value) : '';
        var pageKey = simcardId || window.location.pathname;
        var offerStorageKey = 'irancell_discount_offer_expire_at_' + pageKey;
        var offerDurationMs = 24 * 60 * 60 * 1000;

        function toPersianNumber(value) {
            return String(value).replace(/\d/g, function (digit) {
                return '۰۱۲۳۴۵۶۷۸۹'[digit];
            });
        }

        function getPersistedExpireAt() {
            var storedExpireAt = parseInt(localStorage.getItem(offerStorageKey) || '0', 10);
            if (storedExpireAt > 0) {
                return storedExpireAt;
            }

            var expireAt = Date.now() + offerDurationMs;
            localStorage.setItem(offerStorageKey, String(expireAt));
            return expireAt;
        }

        function render(remainingMs) {
            var totalSeconds = Math.max(0, Math.floor(remainingMs / 1000));
            var hours = Math.floor(totalSeconds / 3600);
            var minutes = Math.floor((totalSeconds % 3600) / 60);
            var seconds = totalSeconds % 60;
            var formatted = String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
            timerElement.textContent = toPersianNumber(formatted);
        }

        var expireAt = getPersistedExpireAt();
        window.setInterval(function () {
            render(expireAt - Date.now());
        }, 1000);

        render(expireAt - Date.now());
    });
}());
