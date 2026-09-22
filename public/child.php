<?php

    declare (strict_types = 1);

    $pageTitle = 'Plantera | Meus filhos';
    require __DIR__ . '/includes/header.php';
?>

<section class="page-header">
    <p class="eyebrow">Acompanhamento</p>
    <h1>Meus filhos</h1>
    <p>Consulte as informações acadêmicas dos alunos vinculados à sua conta.</p>
</section>

<div data-page-message hidden aria-live="polite"></div>

<section class="notice" aria-labelledby="children-title">
    <h2 id="children-title">Alunos vinculados</h2>
    <div data-children-list aria-live="polite"><p>Carregando alunos...</p></div>
</section>

<section class="form-card" data-child-detail hidden aria-live="polite">
    <h2>Detalhes do aluno</h2>
    <div data-child-information></div>
    <h3>Presenças registradas</h3>
    <div data-child-attendance></div>
</section>

<script src="assets/js/academic.js"></script>
<script>
    (function () {
        const api = window.PlanteraAcademic;
        const list = document.querySelector('[data-children-list]');
        const detail = document.querySelector('[data-child-detail]');
        const information = document.querySelector('[data-child-information]');
        const attendance = document.querySelector('[data-child-attendance]');

        async function showChild(studentId) {
            try {
                const result = await api.request('student', 'show', { parameters: { id: studentId } });
                const student = result.data;
                information.textContent = 'Aluno: ' + student.name + '. Matrícula: ' + student.registration + '. Turma: ' + (student.classId || 'não informada') + '.';
                api.renderTable(attendance, [
                    { label: 'Aula', value: item => item.lessonId },
                    { label: 'Presença', value: item => item.status === 1 ? 'Presente' : 'Ausente' },
                ], result.attendance || [], 'Ainda não há presenças registradas para este aluno.');
                detail.hidden = false;
            } catch (error) {
                api.showMessage(error.message, 'error');
            }
        }

        async function loadChildren() {
            try {
                const result = await api.request('student', 'my-children');
                api.renderTable(list, [
                    { label: 'Nome', value: student => student.name },
                    { label: 'Matrícula', value: student => student.registration },
                    { label: 'Turma', value: student => student.classId || 'Sem turma' },
                ], result.data || [], 'Não há alunos vinculados a esta conta.', function (actions, student) {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.textContent = 'Ver detalhes';
                    button.addEventListener('click', function () {
                        showChild(student.id);
                    });
                    while (actions.firstChild) {
                        actions.removeChild(actions.firstChild);
                    }
                    actions.appendChild(button);
                });
            } catch (error) {
                list.textContent = error.message;
            }
        }

        loadChildren();
    }());
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
