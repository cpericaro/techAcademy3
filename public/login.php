<?php

    declare (strict_types = 1);

    if (session_status() === PHP_SESSION_NONE) {
    session_start();
    }

    if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
    }

    $pageTitle = 'Plantera | Entrar';
    require __DIR__ . '/includes/header.php';
?>
<section class="auth-layout" aria-labelledby="login-title">
    <article class="form-card auth-card">
        <p class="eyebrow">Acesso ao sistema</p>
        <h1 id="login-title">Entrar</h1>
        <p>Use seu e-mail ou nome de usuário e sua senha.</p>

        <p id="login-message" class="form-message" role="status" aria-live="polite"></p>

        <form id="login-form">
            <label for="identifier">E-mail ou nome de usuário
                <input id="identifier" name="identifier" autocomplete="username" required>
            </label>
            <label for="password">Senha
                <input id="password" type="password" name="password" autocomplete="current-password" required>
            </label>
            <button type="submit">Entrar</button>
        </form>
    </article>
</section>

<script src="assets/js/auth.js"></script>
<?php require __DIR__ . '/includes/footer.php'; ?>
