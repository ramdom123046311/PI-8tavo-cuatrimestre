// Dark Mode Toggle for Doctor Panel
document.addEventListener('DOMContentLoaded', () => {
    const htmlRoot = document.getElementById('html-root');
    
    if (!htmlRoot) return;
    
    // Initialize dark mode from localStorage
    if (localStorage.getItem('sm_dark_mode') === 'true') {
        htmlRoot.classList.add('dark');
    }
    
    // Create dark mode toggle button in sidebar if sidebar exists
    const sidebar = document.querySelector('aside');
    if (sidebar) {
        const logoutDiv = sidebar.querySelector('div:last-child');
        if (logoutDiv && !logoutDiv.querySelector('[data-dark-toggle]')) {
            const darkToggleBtn = document.createElement('button');
            darkToggleBtn.className = 'flex items-center justify-center gap-2 px-4 py-2 w-full text-xs font-bold text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors mb-3 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700';
            darkToggleBtn.setAttribute('data-dark-toggle', 'true');
            darkToggleBtn.innerHTML = '<i data-lucide="moon" class="w-3.5 h-3.5"></i><span id="dark-mode-label">Modo Oscuro</span>';
            
            darkToggleBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const isDark = htmlRoot.classList.toggle('dark');
                localStorage.setItem('sm_dark_mode', isDark);
                
                // Update Lucide icons if available
                if (window.lucide) {
                    lucide.createIcons();
                }
                
                // Update button label
                const label = darkToggleBtn.querySelector('#dark-mode-label');
                if (label) {
                    label.textContent = isDark ? 'Modo Claro' : 'Modo Oscuro';
                }
            });
            
            // Insert before logout button
            logoutDiv.insertBefore(darkToggleBtn, logoutDiv.firstChild);
            
            // Initialize Lucide icons for the new button
            if (window.lucide) {
                lucide.createIcons();
            }
        }
    }
});
