<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Plantera | Minha conta';
require __DIR__ . '/includes/header.php';
?>
<section class="page-header">
    <p class="eyebrow">Conta</p>
    <h1>Minha conta</h1>
    <p>Confira seus dados e mantenha sua senha atualizada.</p>
</section>

<p id="account-message" class="form-message" role="status" aria-live="polite"></p>

<section class="form-grid account-grid">
    <article class="form-card">
        <h2>Dados cadastrais</h2>
        <form id="account-form">
            <label for="account-username">Nome de usuário
                <input id="account-username" name="username" readonly>
            </label>
            <label for="account-name">Nome
                <input id="account-name" name="name" autocomplete="name" required>
            </label>
            <label for="account-email">E-mail
                <input id="account-email" type="email" name="email" autocomplete="email" required>
            </label>
            <label for="account-cellphone">Celular
                <input id="account-cellphone" name="cellphone" autocomplete="tel" inputmode="tel" required>
            </label>
            <label for="account-cpf">CPF
                <input id="account-cpf" name="cpf" inputmode="numeric" required>
            </label>
            <label for="account-uf">UF
                <input id="account-uf" name="uf" maxlength="2" autocomplete="address-level1" required>
            </label>
            <label for="account-type">Tipo de conta
                <input id="account-type" readonly>
            </label>
            <button type="submit">Salvar alterações</button>
        </form>
    </article>

    <article class="form-card">
        <h2>Alterar senha</h2>
        <form id="password-form">
            <label for="current-password">Senha atual
                <input id="current-password" type="password" name="currentPassword" autocomplete="current-password" required>
            </label>
            <label for="new-password">Nova senha
                <input id="new-password" type="password" name="newPassword" autocomplete="new-password" minlength="8" required>
            </label>
            <label for="confirm-password">Confirme a nova senha
                <input id="confirm-password" type="password" autocomplete="new-password" minlength="8" required>
            </label>
            <button type="submit">Alterar senha</button>
        </form>
    </article>
</section>

<script src="assets/js/account.js"></script>
<?php require __DIR__ . '/includes/footer.php'; ?>
