// Dark Mode Manager
(function() {
    'use strict';
    
    // Get theme from localStorage or default to 'light'
    function getTheme() {
        return localStorage.getItem('theme') || 'light';
    }
    
    // Set theme
    function setTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        
        // Update toggle button if it exists
        updateToggleButton(theme);
    }
    
    // Toggle theme
    function toggleTheme() {
        const currentTheme = getTheme();
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        setTheme(newTheme);
    }
    
    // Update toggle button state
    function updateToggleButton(theme) {
        const toggleBtn = document.getElementById('darkModeToggle');
        if (toggleBtn) {
            const icon = toggleBtn.querySelector('i');
            const text = toggleBtn.querySelector('.toggle-text');
            
            if (theme === 'dark') {
                if (icon) {
                    icon.className = 'fas fa-sun';
                }
                if (text) {
                    text.textContent = 'Mode Terang';
                }
                toggleBtn.classList.add('active');
            } else {
                if (icon) {
                    icon.className = 'fas fa-moon';
                }
                if (text) {
                    text.textContent = 'Mode Gelap';
                }
                toggleBtn.classList.remove('active');
            }
        }
    }
    
    // Initialize theme on page load
    function initTheme() {
        const savedTheme = getTheme();
        setTheme(savedTheme);
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTheme);
    } else {
        initTheme();
    }
    
    // Expose functions globally
    window.darkMode = {
        get: getTheme,
        set: setTheme,
        toggle: toggleTheme
    };
})();
