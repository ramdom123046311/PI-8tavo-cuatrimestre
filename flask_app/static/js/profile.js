document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();

    // Toggle for Dark Mode simple visual interaction
    const toggleBtn = document.querySelector('.bg-gray-200.rounded-full.relative');
    if (toggleBtn) {
        let isDark = false;
        toggleBtn.addEventListener('click', function () {
            isDark = !isDark;
            const innerBtn = this.firstElementChild;
            if (isDark) {
                this.classList.remove('bg-gray-200');
                this.classList.add('bg-pink-primary');
                innerBtn.style.transform = 'translateX(1.25rem)';
            } else {
                this.classList.remove('bg-pink-primary');
                this.classList.add('bg-gray-200');
                innerBtn.style.transform = 'translateX(0)';
            }
        });
    }
});
