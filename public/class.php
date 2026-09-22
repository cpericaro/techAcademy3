<?php

    declare (strict_types = 1);

    $pageTitle = 'Plantera | Turmas';
    require __DIR__ . '/includes/header.php';
?>

<section class="page-header">
    <p class="eyebrow">Organização</p>
    <h1>Turmas</h1>
    <p>Cadastre turmas, associe estudantes e defina os horários de cada uma.</p>
</section>

<div data-page-message hidden aria-live="polite"></div>

<section class="notice" aria-labelledby="class-list-title">
    <h2 id="class-list-title">Turmas cadastradas</h2>
    <div data-class-list aria-live="polite"><p>Carregando turmas...</p></div>
</section>

<section class="form-grid">
    <article class="form-card">
        <h2>Cadastrar turma</h2>
        <form method="post" data-api-form data-resource="class" data-action="create" data-reset="true">
            <label>Nome <input name="name" required></label>
            <label>Ano <input type="number" name="year" min="2000" max="2100" value="2026" required></label>
            <button type="submit">Cadastrar turma</button>
        </form>
    </article>

    <article class="form-card">
        <h2>Consultar turma</h2>
        <form method="get" data-class-show>
            <label>ID da turma <input type="number" name="id" min="1" required></label>
            <button type="submit">Consultar turma</button>
        </form>
        <div data-class-detail aria-live="polite"></div>
    </article>

    <article class="form-card">
        <h2>Editar turma</h2>
        <form method="post" data-api-form data-resource="class" data-action="edit">
            <label>ID da turma <input type="number" name="id" min="1" required></label>
            <label>Nome <input name="name" required></label>
            <label>Ano <input type="number" name="year" min="2000" max="2100" required></label>
            <button type="submit">Salvar alterações</button>
        </form>
    </article>

    <article class="form-card">
        <h2>Alunos da turma</h2>
        <form method="post" data-api-form data-resource="class" data-action="add-student">
            <label>ID da turma <input type="number" name="id" min="1" required></label>
            <label>ID do aluno <input type="number" name="studentId" min="1" required></label>
            <button type="submit">Adicionar aluno</button>
        </form>
        <form method="post" data-api-form data-resource="class" data-action="remove-student" data-confirm="Remover o aluno desta turma?">
            <label>ID da turma <input type="number" name="id" min="1" required></label>
            <label>ID do aluno <input type="number" name="studentId" min="1" required></label>
            <button type="submit">Remover aluno</button>
        </form>
    </article>

    <article class="form-card">
        <h2>Adicionar horário</h2>
        <form method="post" data-api-form data-resource="class" data-action="add-schedule" data-reset="true">
            <label>ID da turma <input type="number" name="id" min="1" required></label>
            <label>Dia da semana <select name="weekday" required><option value="1">Segunda-feira</option><option value="2">Terça-feira</option><option value="3">Quarta-feira</option><option value="4">Quinta-feira</option><option value="5">Sexta-feira</option><option value="6">Sábado</option><option value="7">Domingo</option></select></label>
            <label>Horário inicial <input type="time" name="startTime" required></label>
            <label>Horário final <input type="time" name="endTime" required></label>
            <button type="submit">Adicionar horário</button>
        </form>
    </article>

    <article class="form-card">
        <h2>Excluir ou remover horário</h2>
        <form method="post" data-api-form data-resource="class" data-action="remove-schedule" data-confirm="Remover este horário?">
            <label>ID da turma <input type="number" name="id" min="1" required></label>
            <label>ID do horário <input type="number" name="scheduleId" min="1" required></label>
            <button type="submit">Remover horário</button>
        </form>
        <form method="post" data-api-form data-resource="class" data-action="delete" data-confirm="Excluir esta turma? Esta ação não pode ser desfeita.">
            <label>ID da turma <input type="number" name="id" min="1" required></label>
            <button class="danger" type="submit">Excluir turma</button>
        </form>
    </article>
</section>

<script src="assets/js/academic.js"></script>
<script>
    (function () {
        const api = window.PlanteraAcademic;
        const list = document.querySelector('[data-class-list]');
        const detail = document.querySelector('[data-class-detail]');
        const weekdays = ['', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado', 'Domingo'];

        async function loadClasses() {
            try {
                const result = await api.request('class', 'list');
                api.renderTable(list, [
                    { label: 'ID', value: schoolClass => schoolClass.id },
                    { label: 'Nome', value: schoolClass => schoolClass.name },
                    { label: 'Ano', value: schoolClass => schoolClass.year },
                ], result.data || [], 'Nenhuma turma cadastrada.');
            } catch (error) {
                list.textContent = error.message;
            }
        }

        document.querySelector('[data-class-show]').addEventListener('submit', async function (event) {
            event.preventDefault();
            try {
                const id = new FormData(event.currentTarget).get('id');
                const result = await api.request('class', 'show', { parameters: { id: id } });
                const students = (result.students || []).map(student => student.name).join(', ') || 'Nenhum aluno';
                const schedules = (result.schedules || []).map(schedule => weekdays[schedule.weekday] + ', ' + schedule.startTime + ' às ' + schedule.endTime).join('; ') || 'Nenhum horário';
                detail.textContent = 'Alunos: ' + students + '. Horários: ' + schedules + '.';
                api.showMessage('Consulta realizada.', 'success');
            } catch (error) {
                detail.textContent = '';
                api.showMessage(error.message, 'error');
            }
        });

        api.bindForms(loadClasses);
        loadClasses();
    }());
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
