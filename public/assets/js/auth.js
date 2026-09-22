document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('login-form');
    const message = document.getElementById('login-message');

    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        message.textContent = 'Entrando...';
        message.className = 'form-message';

        try {
            const response = await fetch('index.php?resource=auth&action=login', {
                method: 'POST',
                body: new FormData(form),
            });
            const body = await response.json();

            if (!response.ok) {
                throw new Error(body.error?.message || 'Não foi possível entrar.');
            }

            window.location.assign('dashboard.php');
        } catch (error) {
            message.textContent = error.message;
            message.className = 'form-message error';
        }
    });
});
