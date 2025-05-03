document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.alertify-dismissible').forEach((alertBox) => {
        const alertId = alertBox.dataset.alertId;
        const dismissDuration = parseInt(alertBox.dataset.dismissDuration, 10) || 24; // default 24h
        const dismissKey = `alertify_${alertId}`;

        // Check if dismissed
        const dismissed = localStorage.getItem(dismissKey);
        if (dismissed) {
            const dismissedTime = parseInt(dismissed, 10);
            const now = Date.now();
            const hoursPassed = (now - dismissedTime) / (1000 * 60 * 60);
            if (hoursPassed < dismissDuration) {
                alertBox.style.display = 'none';
                return; // skip adding event listener
            }
        }

        const closeBtn = alertBox.querySelector('.alertify-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                alertBox.style.display = 'none';
                localStorage.setItem(dismissKey, Date.now().toString());
            });
        }
    });
});