document.addEventListener('DOMContentLoaded', function () {
    const accountForm = document.getElementById('account-form');
    const passwordForm = document.getElementById('password-form');
    const message = document.getElementById('account-message');
    const accountTypes = { 1: 'Professor', 2: 'Responsável', 3: 'Administrador' };

    function showMessage(text, isError) {
        message.textContent = text;
        message.className = isError ? 'form-message error' : 'form-message success';
    }

    async function request(url, options) {
        const response = await fetch(url, options);
        const body = await response.json();

        if (!response.ok) {
            throw new Error(body.error?.message || 'Não foi possível concluir a operação.');
        }

        return body.data;
    }

    function fillAccount(user) {
        accountForm.username.value = user.username || '';
        accountForm.name.value = user.name || '';
        accountForm.email.value = user.email || '';
        accountForm.cellphone.value = user.cellphone || '';
        accountForm.cpf.value = user.cpf || '';
        accountForm.uf.value = user.uf || '';
        document.getElementById('account-type').value = accountTypes[user.accountType] || 'Não informado';
    }

    request('index.php?resource=auth&action=me')
        .then(fillAccount)
        .catch(function (error) {
            showMessage(error.message, true);
        });

    accountForm.addEventListener('submit', async function (event) {
        event.preventDefault();

        try {
            const data = await request('index.php?resource=user&action=edit', {
                method: 'POST',
                body: new FormData(accountForm),
            });
            fillAccount(data);
            showMessage('Dados atualizados com sucesso.', false);
        } catch (error) {
            showMessage(error.message, true);
        }
    });

    passwordForm.addEventListener('submit', async function (event) {
        event.preventDefault();

        if (passwordForm.newPassword.value !== document.getElementById('confirm-password').value) {
            showMessage('A confirmação da nova senha não corresponde.', true);
            return;
        }

        const data = new FormData(passwordForm);
        data.append('username', accountForm.username.value);

        try {
            const result = await request('index.php?resource=user&action=change-password', {
                method: 'POST',
                body: data,
            });
            passwordForm.reset();
            showMessage(result.message || 'Senha alterada com sucesso.', false);
        } catch (error) {
            showMessage(error.message, true);
        }
    });
});
