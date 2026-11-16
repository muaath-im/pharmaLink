/**
 * public/assets/js/csrf.js
 * Automatically fetches the session CSRF token and injects it into all POST forms.
 */
document.addEventListener('DOMContentLoaded', function () {
    fetch('/src/config/csrf_token.php', { method: 'GET', credentials: 'same-origin' })
        .then(function (response) {
            if (!response.ok) {
                throw new Error('Network error loading CSRF token');
            }
            return response.json();
        })
        .then(function (data) {
            if (data && data.csrf_token) {
                var forms = document.querySelectorAll('form[method="post" i]');
                forms.forEach(function (form) {
                    if (!form.querySelector('input[name="csrf_token"]')) {
                        var hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'csrf_token';
                        hidden.value = data.csrf_token;
                        form.appendChild(hidden);
                    }
                });
            }
        })
        .catch(function (error) {
            console.warn('Could not initialize CSRF token:', error);
        });
});
