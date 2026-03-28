// registro_usuario.js
document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();

    const btnMedico = document.getElementById('btn-medico');
    const btnPaciente = document.getElementById('btn-paciente');
    const modalMedico = document.getElementById('modal-medico');
    const modalPaciente = document.getElementById('modal-paciente');
    const closeBtns = document.querySelectorAll('.btn-close-modal');

    // Open Doctor Modal
    btnMedico.addEventListener('click', () => {
        modalMedico.classList.remove('hidden');
    });

    // Open Patient Modal
    btnPaciente.addEventListener('click', () => {
        modalPaciente.classList.remove('hidden');
    });

    // Close Modals
    closeBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            modalMedico.classList.add('hidden');
            modalPaciente.classList.add('hidden');
        });
    });

    // Close on click outside
    window.addEventListener('click', (e) => {
        if (e.target === modalMedico) {
            modalMedico.classList.add('hidden');
        }
        if (e.target === modalPaciente) {
            modalPaciente.classList.add('hidden');
        }
    });

    // Set max date for date inputs to today
    const today = new Date().toISOString().split('T')[0];
    const fechaMedico = document.getElementById('fecha-medico');
    const fechaPaciente = document.getElementById('fecha-paciente');
    if (fechaMedico) fechaMedico.setAttribute('max', today);
    if (fechaPaciente) fechaPaciente.setAttribute('max', today);

    // Form submission validation for date
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            const dateInput = form.querySelector('input[type="date"]');
            if (dateInput) {
                const selectedDate = dateInput.value;
                if (selectedDate > today) {
                    e.preventDefault();
                    alert("La fecha de nacimiento no puede ser en el futuro.");
                }
            }
            // For Doctors: ensure they're over 18 or some experience logic (optional, but requested only "validando que no puede haber nacido mañana")
        });
    });
});
