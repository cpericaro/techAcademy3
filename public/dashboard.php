<?php
$pageTitle = 'Plantera | Início';
require __DIR__ . '/includes/header.php';
?>

<section class="page-intro">
    <p class="eyebrow">Gestão escolar</p>
    <h1>Organize o dia a dia da escola.</h1>
    <p>Use os módulos abaixo para cadastrar, consultar, editar ou excluir os registros do sistema.</p>
</section>

<section class="module-grid" aria-label="Módulos do sistema">

    <article class="module-card">
        <p class="module-label">Cadastro</p>
        <h2>Alunos</h2>
        <p>Registre dados do aluno e associe-o a uma turma.</p>
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
</section>

<section class="notice" aria-labelledby="how-to-use">
    <h2 id="how-to-use">Como usar</h2>
    <p>Os formulários enviam os dados diretamente para <code>index.php</code> por <code>POST</code>. Após o envio, a resposta JSON confirma o resultado da operação.</p>
</section>


<?php require __DIR__ . '/includes/footer.php'; ?>
