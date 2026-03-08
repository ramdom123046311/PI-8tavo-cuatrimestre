document.addEventListener('DOMContentLoaded', function () {
    lucide.createIcons();

    const roleSelect = document.querySelector('select[class*="form-select"]');
    const medicalFields = document.getElementById('medicalFields');

    if (roleSelect && medicalFields) {
        roleSelect.addEventListener('change', function () {
            if (this.value === 'medico') {
                medicalFields.classList.remove('d-none');
            } else {
                medicalFields.classList.add('d-none');
            }
        });
    }
});
