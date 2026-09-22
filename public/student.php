<?php

    declare (strict_types = 1);

    $pageTitle = 'Plantera | Alunos';
    require __DIR__ . '/includes/header.php';
?>

<section class="page-header">
    <p class="eyebrow">Cadastro</p>
    <h1>Alunos</h1>
    <p>Cadastre estudantes, associe-os a uma turma e vincule seus responsáveis.</p>
</section>

<div data-page-message hidden aria-live="polite"></div>

<section class="notice" aria-labelledby="student-list-title">
    <h2 id="student-list-title">Alunos cadastrados</h2>
    <div data-student-list aria-live="polite"><p>Carregando alunos...</p></div>
</section>

<section class="form-grid">
    <article class="form-card">
        <h2>Cadastrar aluno</h2>
        <form method="post" data-api-form data-resource="student" data-action="create" data-reset="true">
            <label>Nome <input name="name" required></label>
            <label>Data de nascimento <input type="date" name="birthDate" required></label>
            <label>Matrícula <input name="registration" required></label>
            <label>ID da turma (opcional) <input type="number" name="classId" min="1"></label>
            <button type="submit">Cadastrar aluno</button>
        </form>
    </article>

    <article class="form-card">
        <h2>Consultar aluno</h2>
        <form method="get" data-api-form data-api-ignore data-resource="student" data-action="show">
            <label>ID do aluno <input type="number" name="id" min="1" required></label>
            <button type="submit">Consultar aluno</button>
        </form>
        <div data-student-detail aria-live="polite"></div>
    </article>

    <article class="form-card">
        <h2>Editar aluno</h2>
        <form method="post" data-api-form data-resource="student" data-action="edit">
            <label>ID do aluno <input type="number" name="id" min="1" required></label>
            <label>Nome <input name="name" required></label>
            <label>Data de nascimento <input type="date" name="birthDate" required></label>
            <label>Matrícula <input name="registration" required></label>
            <button type="submit">Salvar alterações</button>
        </form>
    </article>

    <article class="form-card">
        <h2>Turma do aluno</h2>
        <form method="post" data-api-form data-resource="student" data-action="assign-class">
            <label>ID do aluno <input type="number" name="id" min="1" required></label>
            <label>ID da turma <input type="number" name="classId" min="1" required></label>
            <button type="submit">Associar turma</button>
        </form>
        <form method="post" data-api-form data-resource="student" data-action="remove-class" data-confirm="Remover o aluno da turma?">
            <label>ID do aluno <input type="number" name="id" min="1" required></label>
            <button type="submit">Remover da turma</button>
        </form>
    </article>

    <article class="form-card">
        <h2>Responsável</h2>
        <form method="post" data-api-form data-resource="student" data-action="link-user">
            <label>ID do aluno <input type="number" name="id" min="1" required></label>
            <label>ID do usuário <input type="number" name="userId" min="1" required></label>
            <label>Parentesco <input name="relationship" placeholder="Mãe, pai ou responsável" required></label>
            <button type="submit">Vincular responsável</button>
        </form>
    </article>

    <article class="form-card">
        <h2>Excluir aluno</h2>
        <form method="post" data-api-form data-resource="student" data-action="delete" data-confirm-name="true">
            <label>ID do aluno <input type="number" name="id" min="1" required></label>
            <p>Para confirmar, será necessário digitar exatamente o nome do aluno.</p>
            <button class="danger" type="submit">Excluir aluno</button>
        </form>
    </article>
</section>

<script src="assets/js/academic.js"></script>
<script>
    (function () {
        const api = window.PlanteraAcademic;
        const list = document.querySelector('[data-student-list]');
        const detail = document.querySelector('[data-student-detail]');

        async function loadStudents() {
            try {
                const result = await api.request('student', 'list');
                api.renderTable(list, [
                    { label: 'ID', value: student => student.id },
                    { label: 'Nome', value: student => student.name },
                    { label: 'Matrícula', value: student => student.registration },
                    { label: 'Turma', value: student => student.classId || 'Sem turma' },
                ], result.data || [], 'Nenhum aluno cadastrado.');
            } catch (error) {
                list.textContent = error.message;
            }
        }

        document.querySelector('form[data-action="show"]').addEventListener('submit', async function (event) {
            event.preventDefault();
            try {
                const id = new FormData(event.currentTarget).get('id');
                const result = await api.request('student', 'show', { parameters: { id: id } });
                const student = result.data;
                detail.textContent = 'Aluno: ' + student.name + '. Matrícula: ' + student.registration + '. Turma: ' + (student.classId || 'não informada') + '.';
                api.showMessage('Consulta realizada.', 'success');
            } catch (error) {
                detail.textContent = '';
                api.showMessage(error.message, 'error');
            }
        });

        api.bindForms(loadStudents);
        loadStudents();
    }());
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
