document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();

    // Terms and Conditions Modal Logic
    const termsModal = document.getElementById('terms-modal');
    const btnAcceptTerms = document.getElementById('btn-accept-terms');
    const btnRejectTerms = document.getElementById('btn-reject-terms');

    // Show modal on load (simulating terms check)
    if (termsModal) {
        termsModal.classList.remove('hidden');
    }

    if (btnAcceptTerms) {
        btnAcceptTerms.addEventListener('click', () => {
            termsModal.classList.add('hidden');
            // Here you would normally save the acceptance to the backend
        });
    }

    if (btnRejectTerms) {
        btnRejectTerms.addEventListener('click', () => {
            termsModal.classList.add('hidden');
            // Here you would normally handle rejection (e.g., redirect to logout)
        });
    }
});
