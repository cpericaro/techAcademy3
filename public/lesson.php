<?php
$pageTitle = 'Plantera | Aulas';
require __DIR__ . '/includes/header.php';
?>
<section class="page-header">
    <p class="eyebrow">Acompanhamento</p>
    <h1>Aulas e presença</h1>
    <p>Registre o planejamento, o conteúdo aplicado e a presença dos alunos de cada turma.</p>
</section>
<p class="api-link"><a href="index.php?resource=lesson&action=list">Consultar lista de aulas em JSON</a></p>

<section class="form-grid">
    <article class="form-card">
        <h2>Cadastrar aula</h2>
        <form action="index.php?resource=lesson&amp;action=create" method="post">
            <label>ID da turma <input type="number" name="classId" min="1" required></label>
            <label>Data <input type="date" name="lessonDate" required></label>
            <label>Conteúdo planejado <textarea name="plannedContent" required></textarea></label>
            <label>Conteúdo aplicado <textarea name="content"></textarea></label>
            <button type="submit">Cadastrar aula</button>
        </form>
    </article>

    <article class="form-card">
        <h2>Consultar aula</h2>
        <form action="index.php" method="get">
            <input type="hidden" name="resource" value="lesson">
            <input type="hidden" name="action" value="show">
            <label>ID da aula <input type="number" name="id" min="1" required></label>
            <button type="submit">Consultar aula</button>
        </form>
    </article>

    <article class="form-card">
        <h2>Editar aula</h2>
        <form action="index.php?resource=lesson&amp;action=edit" method="post">
            <label>ID da aula <input type="number" name="id" min="1" required></label>
            <label>Data <input type="date" name="lessonDate" required></label>
            <label>Conteúdo planejado <textarea name="plannedContent" required></textarea></label>
            <label>Conteúdo aplicado <textarea name="content"></textarea></label>
            <label>Status <select name="status" required><option value="1">Ativa</option><option value="0">Cancelada</option></select></label>
            <button type="submit">Salvar alterações</button>
        </form>
    </article>

    <article class="form-card">
        <h2>Registrar presença</h2>
        <form action="index.php?resource=lesson&amp;action=register-attendance" method="post">
            <label>ID da aula <input type="number" name="id" min="1" required></label>
            <label>ID do aluno <input type="number" name="studentId" min="1" required></label>
            <label>Status <select name="status" required><option value="1">Presente</option><option value="2">Ausente</option></select></label>
            <button type="submit">Registrar presença</button>
        </form>
    </article>
    
    <article class="form-card">
        <h2>Excluir aula</h2>
        <form action="index.php?resource=lesson&amp;action=delete" method="post">
            <label>ID da aula <input type="number" name="id" min="1" required></label>
            <button class="danger" type="submit">Excluir aula</button>
        </form>
    </article>

</section>


<?php require __DIR__ . '/includes/footer.php'; ?>
