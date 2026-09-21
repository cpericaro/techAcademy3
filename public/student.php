<?php
    $pageTitle = 'Plantera | Alunos';
    require __DIR__ . '/includes/header.php';
?>
<section class="page-header">
    <p class="eyebrow">Cadastro</p>
    <h1>Alunos</h1>
    <p>Cadastre o aluno primeiro. A turma pode ser informada no cadastro ou associada depois.</p>
</section>
<p class="api-link"><a href="index.php?resource=student&action=list">Consultar lista de alunos em JSON</a></p>

<section class="form-grid">
    <article class="form-card">
        <h2>Cadastrar aluno</h2>
        <form action="index.php?resource=student&amp;action=create" method="post">
            <label>Nome <input name="name" required></label>
            <label>Data de nascimento <input type="date" name="birthDate" required></label>
            <label>Matrícula <input name="registration" required></label>
            <label>ID da turma (opcional) <input type="number" name="classId" min="1"></label>
            <button type="submit">Cadastrar aluno</button>
        </form>
    </article>

    <article class="form-card">
        <h2>Consultar aluno</h2>
        <form action="index.php" method="get">
            <input type="hidden" name="resource" value="student">
            <input type="hidden" name="action" value="show">
            <label>ID do aluno <input type="number" name="id" min="1" required></label>
            <button type="submit">Consultar aluno</button>
        </form>
    </article>

    <article class="form-card">
        <h2>Editar aluno</h2>
        <form action="index.php?resource=student&amp;action=edit" method="post">
            <label>ID do aluno <input type="number" name="id" min="1" required></label>
            <label>Nome <input name="name" required></label>
            <label>Data de nascimento <input type="date" name="birthDate" required></label>
            <label>Matrícula <input name="registration" required></label>
            <button type="submit">Salvar alterações</button>
        </form>
    </article>

    <article class="form-card">
        <h2>Turma do aluno</h2>
        <form action="index.php?resource=student&amp;action=assign-class" method="post">
            <label>ID do aluno <input type="number" name="id" min="1" required></label>
            <label>ID da turma <input type="number" name="classId" min="1" required></label>
            <button type="submit">Associar turma</button>
        </form>
        <form action="index.php?resource=student&amp;action=remove-class" method="post">
            <label>ID do aluno <input type="number" name="id" min="1" required></label>
            <button type="submit">Remover da turma</button>
        </form>
    </article>

    <article class="form-card">
        <h2>Responsável</h2>
        <form action="index.php?resource=student&amp;action=link-user" method="post">
            <label>ID do aluno <input type="number" name="id" min="1" required></label>
            <label>ID do usuário <input type="number" name="userId" min="1" required></label>
            <label>Parentesco <input name="relationship" placeholder="Mãe, pai ou responsável" required></label>
            <button type="submit">Vincular responsável</button>
        </form>
    </article>

    <article class="form-card">
        <h2>Excluir aluno</h2>
        <form action="index.php?resource=student&amp;action=delete" method="post">
            <label>ID do aluno <input type="number" name="id" min="1" required></label>
            <button class="danger" type="submit">Excluir aluno</button>
        </form>
    </article>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
