<?php

    declare (strict_types = 1);

    $pageTitle = 'Plantera | Início';
    require __DIR__ . '/includes/header.php';
?>

<section class="page-intro">
    <p class="eyebrow">Gestão escolar</p>
    <h1>Organize o dia a dia da escola.</h1>
    <p>Escolha um módulo para consultar e manter os registros acadêmicos.</p>
</section>

<section class="module-grid" aria-label="Módulos do sistema">
    <?php if ($isParent): ?>
        <article class="module-card">
            <p class="module-label">Acompanhamento</p>
            <h2>Meus filhos</h2>
            <p>Consulte turma, aulas e presenças dos alunos vinculados à sua conta.</p>
            <a href="child.php">Ver meus filhos</a>
        </article>
    <?php else: ?>
    <article class="module-card">
        <p class="module-label">Cadastro</p>
        <h2>Alunos</h2>
        <p>Registre os dados do aluno, a matrícula, a turma e os responsáveis.</p>
        <a href="student.php">Gerenciar alunos</a>
    </article>

    <article class="module-card">
        <p class="module-label">Organização</p>
        <h2>Turmas</h2>
        <p>Defina o ano, os estudantes e os horários de cada turma.</p>
        <a href="class.php">Gerenciar turmas</a>
    </article>

    <article class="module-card">
        <p class="module-label">Acompanhamento</p>
        <h2>Aulas</h2>
        <p>Registre planejamento, conteúdo aplicado e presença.</p>
        <a href="lesson.php">Gerenciar aulas</a>
    </article>
    <?php endif; ?>
</section>

<section class="notice" aria-labelledby="how-to-use">
    <h2 id="how-to-use">Como usar</h2>
    <p>As telas consomem a API JSON do sistema e apresentam o resultado em listas e mensagens na própria página.</p>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
