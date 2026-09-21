<?php
    $pageTitle = 'Plantera | Turmas';
    require __DIR__ . '/includes/header.php';
?>
<section class="page-header">
    <p class="eyebrow">Organização</p>
    <h1>Turmas</h1>
    <p>Cadastre a turma e use o identificador retornado para associar alunos e horários.</p>
</section>
<p class="api-link"><a href="index.php?resource=class&action=list">Consultar lista de turmas em JSON</a></p>

<section class="form-grid">
    <article class="form-card">
        <h2>Cadastrar turma</h2>
        <form action="index.php?resource=class&amp;action=create" method="post">
            <label>Nome <input name="name" required></label>
            <label>Ano <input type="number" name="year" min="2000" max="2100" value="2026" required></label>
            <button type="submit">Cadastrar turma</button>
        </form>
    </article>

    <article class="form-card">
        <h2>Consultar turma</h2>
        <form action="index.php" method="get">
            <input type="hidden" name="resource" value="class">
            <input type="hidden" name="action" value="show">
            <label>ID da turma <input type="number" name="id" min="1" required></label>
            <button type="submit">Consultar turma</button>
        </form>
    </article>

    <article class="form-card">
        <h2>Editar turma</h2>
        <form action="index.php?resource=class&amp;action=edit" method="post">
            <label>ID da turma <input type="number" name="id" min="1" required></label>
            <label>Nome <input name="name" required></label>
            <label>Ano <input type="number" name="year" min="2000" max="2100" required></label>
            <button type="submit">Salvar alterações</button>
        </form>
    </article>

    <article class="form-card">
        <h2>Alunos da turma</h2>
        <form action="index.php?resource=class&amp;action=add-student" method="post">
            <label>ID da turma <input type="number" name="id" min="1" required></label>
            <label>ID do aluno <input type="number" name="studentId" min="1" required></label>
            <button type="submit">Adicionar aluno</button>
        </form>

        <form action="index.php?resource=class&amp;action=remove-student" method="post">
            <label>ID da turma <input type="number" name="id" min="1" required></label>
            <label>ID do aluno <input type="number" name="studentId" min="1" required></label>
            <button type="submit">Remover aluno</button>
        </form>

    </article>

    <article class="form-card">
        <h2>Adicionar horário</h2>

        <form action="index.php?resource=class&amp;action=add-schedule" method="post">
            <label>ID da turma <input type="number" name="id" min="1" required></label>
            <label>Dia da semana <select name="weekday" required><option value="1">Segunda-feira</option><option value="2">Terça-feira</option><option value="3">Quarta-feira</option><option value="4">Quinta-feira</option><option value="5">Sexta-feira</option><option value="6">Sábado</option><option value="7">Domingo</option></select></label>
            <label>Horário inicial <input type="time" name="startTime" required></label>
            <label>Horário final <input type="time" name="endTime" required></label>
            <button type="submit">Adicionar horário</button>
        </form>

    </article>

    <article class="form-card">
        <h2>Excluir turma</h2>
        <form action="index.php?resource=class&amp;action=remove-schedule" method="post">
            <label>ID da turma <input type="number" name="id" min="1" required></label>
            <label>ID do horário <input type="number" name="scheduleId" min="1" required></label>
            <button type="submit">Remover horário</button>
        </form>

        <form action="index.php?resource=class&amp;action=delete" method="post">
            <label>ID da turma <input type="number" name="id" min="1" required></label>
            <button class="danger" type="submit">Excluir turma</button>
        </form>

    </article>

</section>


<?php require __DIR__ . '/includes/footer.php'; ?>
