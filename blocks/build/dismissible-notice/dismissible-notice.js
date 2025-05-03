/******/ (() => { // webpackBootstrap
/*!******************************************************!*\
  !*** ./src/dismissible-notice/dismissible-notice.js ***!
  \******************************************************/
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.alertify-dismissible').forEach(alertBox => {
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
        // 🔥 Instead of hiding with CSS, REMOVE from DOM completely:
        alertBox.remove();
        return; // Skip adding event listener
      }
    }

    // Add event listener for close button
    const closeBtn = alertBox.querySelector('.alertify-close');
    if (closeBtn) {
      closeBtn.addEventListener('click', () => {
        alertBox.remove(); // Remove from DOM immediately
        localStorage.setItem(dismissKey, Date.now().toString());
      });
    }
  });
});
/******/ })()
;
//# sourceMappingURL=dismissible-notice.js.map