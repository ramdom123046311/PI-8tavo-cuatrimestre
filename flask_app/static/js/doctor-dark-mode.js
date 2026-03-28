/**
 * doctor-dark-mode.js — Sistema de modo oscuro para doctores
 * Maneja toggle persistente con localStorage
 */

document.addEventListener('DOMContentLoaded', () => {
    const htmlRoot = document.documentElement;
    const darToggleBtn = document.getElementById('dark-toggle-btn');
    
    if (!darToggleBtn) return;
    
    // Restaurar preferencia guardada
    const isDarkMode = localStorage.getItem('doctor_dark_mode') === 'true';
    applyDarkMode(isDarkMode);
    
    // Event listener para el botón
    darToggleBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        
        const currentStatus = localStorage.getItem('doctor_dark_mode') === 'true';
        const newStatus = !currentStatus;
        
        applyDarkMode(newStatus);
        localStorage.setItem('doctor_dark_mode', newStatus);
        
        // Re-render Lucide icons
        if (window.lucide) {
            lucide.createIcons();
        }
    });
    
    /**
     * Aplica o remueve la clase dark al HTML
     */
    function applyDarkMode(isDark) {
        if (isDark) {
            htmlRoot.classList.add('doctor-dark');
            // Actualizar botón visual
            if (darToggleBtn) {
                darToggleBtn.setAttribute('data-dark', 'true');
                const icon = darToggleBtn.querySelector('[data-lucide]');
                if (icon) {
                    icon.setAttribute('data-lucide', 'sun');
                }
                const label = darToggleBtn.querySelector('[data-label]');
                if (label) {
                    label.textContent = 'Modo Claro';
                }
            }
        } else {
            htmlRoot.classList.remove('doctor-dark');
            // Actualizar botón visual
            if (darToggleBtn) {
                darToggleBtn.setAttribute('data-dark', 'false');
                const icon = darToggleBtn.querySelector('[data-lucide]');
                if (icon) {
                    icon.setAttribute('data-lucide', 'moon');
                }
                const label = darToggleBtn.querySelector('[data-label]');
                if (label) {
                    label.textContent = 'Modo Oscuro';
                }
            }
        }
        
        // Re-render Lucide icons
        if (window.lucide) {
            setTimeout(() => {
                lucide.createIcons();
            }, 0);
        }
    }
});
