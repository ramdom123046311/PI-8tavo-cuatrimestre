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

    // Form submission
    const formMedico = document.getElementById('form-medico');
    const modalSuccess = document.getElementById('modal-success');
    const btnCloseSuccess = document.getElementById('btn-close-success');

    if (formMedico) {
        formMedico.addEventListener('submit', (e) => {
            e.preventDefault();
            modalMedico.classList.add('hidden');
            modalSuccess.classList.remove('hidden');
        });
    }

    if (btnCloseSuccess) {
        btnCloseSuccess.addEventListener('click', () => {
            modalSuccess.classList.add('hidden');
            window.location.href = '/login';
        });
    }
});
