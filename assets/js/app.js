// Minimal JS for Online Library

// Example: enable Bootstrap client-side validation styling
(function () {
    'use strict';
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();

// Optional: simple dark mode toggle using localStorage
(function () {
    var toggle = document.getElementById('darkModeToggle');
    if (!toggle) return;

    var stored = localStorage.getItem('ol-theme');
    if (stored === 'dark') {
        document.documentElement.setAttribute('data-bs-theme', 'dark');
        toggle.checked = true;
    }

    toggle.addEventListener('change', function () {
        if (toggle.checked) {
            document.documentElement.setAttribute('data-bs-theme', 'dark');
            localStorage.setItem('ol-theme', 'dark');
        } else {
            document.documentElement.removeAttribute('data-bs-theme');
            localStorage.setItem('ol-theme', 'light');
        }
    });
})();