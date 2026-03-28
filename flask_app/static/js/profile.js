/**
 * profile.js — Salud Materna
 * Dark mode persistente + preview de foto de perfil
 */

document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();

    // ── Dark Mode ─────────────────────────────────────────────────────────────
    const htmlEl   = document.getElementById('html-root');
    const toggle   = document.getElementById('dark-toggle');
    const thumb    = document.getElementById('dark-thumb');
    const darkIcon = document.getElementById('dark-icon');

    // Restaurar preferencia guardada
    const isDarkSaved = localStorage.getItem('sm_dark_mode') === 'true';
    applyDark(isDarkSaved);

    toggle.addEventListener('click', () => {
        const nowDark = !htmlEl.classList.contains('dark');
        applyDark(nowDark);
        localStorage.setItem('sm_dark_mode', nowDark);
    });

    function applyDark(dark) {
        if (dark) {
            htmlEl.classList.add('dark');
            toggle.classList.remove('bg-gray-200');
            toggle.classList.add('bg-pink-primary');
            thumb.style.transform = 'translateX(1.25rem)';
            toggle.setAttribute('aria-checked', 'true');
            if (darkIcon) { darkIcon.setAttribute('data-lucide', 'moon'); }
        } else {
            htmlEl.classList.remove('dark');
            toggle.classList.remove('bg-pink-primary');
            toggle.classList.add('bg-gray-200');
            thumb.style.transform = 'translateX(0)';
            toggle.setAttribute('aria-checked', 'false');
            if (darkIcon) { darkIcon.setAttribute('data-lucide', 'sun'); }
        }
        // Re-render lucide icon
        lucide.createIcons();
    }

    // ── Preview Foto de Perfil ────────────────────────────────────────────────
    const btnChangePhoto = document.getElementById('btn-change-photo');
    const fotoInput      = document.getElementById('foto-input');
    const avatarPreview  = document.getElementById('avatar-preview');
    const avatarIcon     = document.getElementById('avatar-icon');    // puede ser null

    if (btnChangePhoto && fotoInput) {
        btnChangePhoto.addEventListener('click', () => fotoInput.click());

        fotoInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;

            // Validar tamaño (5MB máx)
            if (file.size > 5 * 1024 * 1024) {
                alert('La imagen no puede superar los 5MB.');
                fotoInput.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = (ev) => {
                // Mostrar imagen en el círculo de perfil
                if (avatarPreview) {
                    avatarPreview.src = ev.target.result;
                    avatarPreview.classList.remove('hidden');
                }
                // Ocultar el icono genérico si existe
                if (avatarIcon) {
                    avatarIcon.style.display = 'none';
                }
                // Actualizar avatar del sidebar también
                const sidebarImg = document.getElementById('sidebar-avatar-img');
                const sidebarIcon = document.getElementById('sidebar-avatar-icon');
                if (sidebarImg) {
                    sidebarImg.src = ev.target.result;
                } else if (sidebarIcon) {
                    // Crear img en el sidebar si aún no existía
                    const wrap = document.getElementById('sidebar-avatar-wrap');
                    if (wrap) {
                        sidebarIcon.remove();
                        const img = document.createElement('img');
                        img.src = ev.target.result;
                        img.alt = 'avatar';
                        img.className = 'w-10 h-10 object-cover rounded-full';
                        wrap.appendChild(img);
                    }
                }
            };
            reader.readAsDataURL(file);
        });
    }

    // ── Auto-dismiss flash messages ───────────────────────────────────────────
    const flashes = document.querySelectorAll('.flash-msg');
    flashes.forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        }, 4000);
    });
});
