<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'Plantera';
$currentPage = basename($_SERVER['PHP_SELF']);
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
            <a class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">Início</a>
            <a class="<?= $currentPage === 'student.php' ? 'active' : '' ?>" href="student.php">Alunos</a>
            <a class="<?= $currentPage === 'class.php' ? 'active' : '' ?>" href="class.php">Turmas</a>
            <a class="<?= $currentPage === 'lesson.php' ? 'active' : '' ?>" href="lesson.php">Aulas</a>
        </nav>
    </div>
</header>
<main class="container">
