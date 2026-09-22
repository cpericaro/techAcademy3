<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'Plantera';
$currentPage = basename($_SERVER['PHP_SELF']);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$accountType = isset($_SESSION['account_type']) ? (int) $_SESSION['account_type'] : null;
$isAuthenticated = isset($_SESSION['user_id']);
$isAdmin = $accountType === 3;
$isParent = $accountType === 2;

if (!$isAuthenticated && $currentPage !== 'login.php') {
    header('Location: login.php');
    exit;
}

if ($isAuthenticated && $isParent && in_array($currentPage, ['student.php', 'class.php', 'lesson.php', 'users.php'], true)) {
    header('Location: child.php');
    exit;
}

if ($isAuthenticated && !$isAdmin && $currentPage === 'users.php') {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-content">
        <a class="brand" href="dashboard.php">Plantera <span>Gestão escolar</span></a>
        <nav aria-label="Navegação principal">
            <?php if (!$isAuthenticated): ?>
                <a class="<?= $currentPage === 'login.php' ? 'active' : '' ?>" href="login.php">Entrar</a>
            <?php else: ?>
                <a class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">Início</a>
                <?php if ($isParent): ?>
                    <a class="<?= $currentPage === 'child.php' ? 'active' : '' ?>" href="child.php">Meus filhos</a>
                <?php else: ?>
                    <a class="<?= $currentPage === 'student.php' ? 'active' : '' ?>" href="student.php">Alunos</a>
                    <a class="<?= $currentPage === 'class.php' ? 'active' : '' ?>" href="class.php">Turmas</a>
                    <a class="<?= $currentPage === 'lesson.php' ? 'active' : '' ?>" href="lesson.php">Aulas</a>
                <?php endif; ?>
                <?php if ($isAdmin): ?>
                    <a class="<?= $currentPage === 'users.php' ? 'active' : '' ?>" href="users.php">Usuários</a>
                <?php endif; ?>
                <a class="<?= $currentPage === 'account.php' ? 'active' : '' ?>" href="account.php">Minha conta</a>
                <a href="logout.php">Sair</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container">
