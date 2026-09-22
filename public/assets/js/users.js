document.addEventListener('DOMContentLoaded', function () {
    const createForm = document.getElementById('create-user-form');
    const findForm = document.getElementById('find-user-form');
    const editForm = document.getElementById('edit-user-form');
    const deleteButton = document.getElementById('delete-user-button');
    const message = document.getElementById('users-message');

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

    function fillEditForm(user) {
        editForm.username.value = user.username || '';
        editForm.name.value = user.name || '';
        editForm.email.value = user.email || '';
        editForm.cellphone.value = user.cellphone || '';
        editForm.cpf.value = user.cpf || '';
        editForm.uf.value = user.uf || '';
        editForm.hidden = false;
    }

    createForm.addEventListener('submit', async function (event) {
        event.preventDefault();

        try {
            const user = await request('index.php?resource=user&action=create', {
                method: 'POST',
                body: new FormData(createForm),
            });
            createForm.reset();
            showMessage('Usuário ' + user.username + ' cadastrado com sucesso.', false);
        } catch (error) {
            showMessage(error.message, true);
        }
    });

    findForm.addEventListener('submit', async function (event) {
        event.preventDefault();

        try {
            const username = encodeURIComponent(findForm.username.value);
            const user = await request('index.php?resource=user&action=show&username=' + username);
            fillEditForm(user);
            showMessage('Dados carregados.', false);
        } catch (error) {
            editForm.hidden = true;
            showMessage(error.message, true);
        }
    });

    editForm.addEventListener('submit', async function (event) {
        event.preventDefault();

        try {
            const user = await request('index.php?resource=user&action=edit', {
                method: 'POST',
                body: new FormData(editForm),
            });
            fillEditForm(user);
            showMessage('Dados atualizados com sucesso.', false);
        } catch (error) {
            showMessage(error.message, true);
        }
    });

    deleteButton.addEventListener('click', async function () {
        const username = editForm.username.value;

        if (!window.confirm('Excluir o usuário ' + username + '?')) {
            return;
        }

        const data = new FormData();
        data.append('username', username);

        try {
            const result = await request('index.php?resource=user&action=delete', {
                method: 'POST',
                body: data,
            });
            editForm.reset();
            editForm.hidden = true;
            showMessage(result.message || 'Conta excluída com sucesso.', false);
        } catch (error) {
            showMessage(error.message, true);
        }
    });
});
