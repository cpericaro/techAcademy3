<?php

    declare (strict_types = 1);

    $pageTitle = 'Plantera | Aulas';
    require __DIR__ . '/includes/header.php';
?>

<section class="page-header">
    <p class="eyebrow">Acompanhamento</p>
    <h1>Aulas e presença</h1>
    <p>Registre o planejamento, o conteúdo aplicado e a presença dos alunos de cada turma.</p>
</section>

<div data-page-message hidden aria-live="polite"></div>

<section class="notice" aria-labelledby="lesson-list-title">
    <h2 id="lesson-list-title">Aulas cadastradas</h2>
    <div data-lesson-list aria-live="polite"><p>Carregando aulas...</p></div>
</section>

<section class="form-grid">
    <article class="form-card">
        <h2>Cadastrar aula</h2>
        <form method="post" data-api-form data-resource="lesson" data-action="create" data-reset="true">
            <label>ID da turma <input type="number" name="classId" min="1" required></label>
            <label>Data <input type="date" name="lessonDate" required></label>
            <label>Conteúdo planejado <textarea name="plannedContent" required></textarea></label>
            <label>Conteúdo aplicado <textarea name="content"></textarea></label>
            <button type="submit">Cadastrar aula</button>
        </form>
    </article>

    <article class="form-card">
        <h2>Consultar aula</h2>
        <form method="get" data-lesson-show>
            <label>ID da aula <input type="number" name="id" min="1" required></label>
            <button type="submit">Consultar aula</button>
        </form>
        <div data-lesson-detail aria-live="polite"></div>
    </article>

    <article class="form-card">
        <h2>Editar aula</h2>
        <form method="post" data-api-form data-resource="lesson" data-action="edit">
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
        <form method="post" data-api-form data-resource="lesson" data-action="register-attendance">
            <label>ID da aula <input type="number" name="id" min="1" required></label>
            <label>ID do aluno <input type="number" name="studentId" min="1" required></label>
            <label>Status <select name="status" required><option value="1">Presente</option><option value="2">Ausente</option></select></label>
            <button type="submit">Registrar presença</button>
        </form>
    </article>

    <article class="form-card">
        <h2>Excluir aula</h2>
        <form method="post" data-api-form data-resource="lesson" data-action="delete" data-confirm="Excluir esta aula? Esta ação não pode ser desfeita.">
            <label>ID da aula <input type="number" name="id" min="1" required></label>
            <button class="danger" type="submit">Excluir aula</button>
        </form>
    </article>
</section>

<script src="assets/js/academic.js"></script>
<script>
    (function () {
        const api = window.PlanteraAcademic;
        const list = document.querySelector('[data-lesson-list]');
        const detail = document.querySelector('[data-lesson-detail]');

        async function loadLessons() {
            try {
                const result = await api.request('lesson', 'list');
                api.renderTable(list, [
                    { label: 'ID', value: lesson => lesson.id },
                    { label: 'Data', value: lesson => lesson.lessonDate },
                    { label: 'Turma', value: lesson => lesson.classId },
                    { label: 'Status', value: lesson => lesson.status === 1 ? 'Ativa' : 'Cancelada' },
                    { label: 'Planejamento', value: lesson => lesson.plannedContent },
                ], result.data || [], 'Nenhuma aula cadastrada.');
            } catch (error) {
                list.textContent = error.message;
            }
        }

        document.querySelector('[data-lesson-show]').addEventListener('submit', async function (event) {
            event.preventDefault();
            try {
                const id = new FormData(event.currentTarget).get('id');
                const result = await api.request('lesson', 'show', { parameters: { id: id } });
                const lesson = result.data;
                const attendance = (result.attendance || []).length;
                detail.textContent = 'Data: ' + lesson.lessonDate + '. Turma: ' + lesson.classId + '. Registros de presença: ' + attendance + '.';
                api.showMessage('Consulta realizada.', 'success');
            } catch (error) {
                detail.textContent = '';
                api.showMessage(error.message, 'error');
            }
        });

        api.bindForms(loadLessons);
        loadLessons();
    }());
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
