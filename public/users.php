<?php

    declare (strict_types = 1);

    if (session_status() === PHP_SESSION_NONE) {
    session_start();
    }

    if ((int) ($_SESSION['account_type'] ?? 0) !== 3) {
    header('Location: dashboard.php');
    exit;
    }

    $pageTitle = 'Plantera | Usuários';
    require __DIR__ . '/includes/header.php';
?>
<section class="page-header">
    <p class="eyebrow">Administração</p>
    <h1>Usuários</h1>
    <p>Cadastre e atualize contas de professores, responsáveis e administradores.</p>
</section>

<p id="users-message" class="form-message" role="status" aria-live="polite"></p>

<section class="form-grid users-grid">
    <article class="form-card">
        <h2>Cadastrar usuário</h2>
        <form id="create-user-form">
            <label for="create-username">Nome de usuário <input id="create-username" name="username" required></label>
            <label for="create-name">Nome <input id="create-name" name="name" required></label>
            <label for="create-email">E-mail <input id="create-email" type="email" name="email" required></label>
            <label for="create-cellphone">Celular <input id="create-cellphone" name="cellphone" inputmode="tel" required></label>
            <label for="create-cpf">CPF <input id="create-cpf" name="cpf" inputmode="numeric" required></label>
            <label for="create-uf">UF <input id="create-uf" name="uf" maxlength="2" required></label>
            <label for="create-account-type">Tipo de conta
                <select id="create-account-type" name="accountType" required>
                    <option value="1">Professor</option>
                    <option value="2">Responsável</option>
                    <option value="3">Administrador</option>
                </select>
            </label>
            <label for="create-password">Senha <input id="create-password" type="password" name="password" minlength="8" required></label>
            <button type="submit">Cadastrar usuário</button>
        </form>
    </article>

    <article class="form-card">
        <h2>Consultar e editar</h2>
        <form id="find-user-form">
            <label for="find-username">Nome de usuário <input id="find-username" name="username" required></label>
            <button type="submit">Consultar usuário</button>
        </form>

        <form id="edit-user-form" hidden>
            <label for="edit-username">Nome de usuário <input id="edit-username" name="username" readonly></label>
            <label for="edit-name">Nome <input id="edit-name" name="name" required></label>
            <label for="edit-email">E-mail <input id="edit-email" type="email" name="email" required></label>
            <label for="edit-cellphone">Celular <input id="edit-cellphone" name="cellphone" inputmode="tel" required></label>
            <label for="edit-cpf">CPF <input id="edit-cpf" name="cpf" inputmode="numeric" required></label>
            <label for="edit-uf">UF <input id="edit-uf" name="uf" maxlength="2" required></label>
            <button type="submit">Salvar alterações</button>
            <button class="danger" id="delete-user-button" type="button">Excluir usuário</button>
        </form>
    </article>
</section>

<script src="assets/js/users.js"></script>
<?php require __DIR__ . '/includes/footer.php'; ?>
