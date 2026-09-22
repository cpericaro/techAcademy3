<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Carlos\TechAcademy3\Repository\ClassRepository;
use Carlos\TechAcademy3\Repository\LessonRepository;
use Carlos\TechAcademy3\Repository\StudentRepository;
use Carlos\TechAcademy3\Repository\UserRepository;
use Carlos\TechAcademy3\Service\ClassService;
use Carlos\TechAcademy3\Service\LessonService;
use Carlos\TechAcademy3\Service\StudentService;
use Carlos\TechAcademy3\Service\UserService;

require_once __DIR__ . '/../src/model/enum/AttendanceStatus.php';
require_once __DIR__ . '/../src/model/enum/AccountType.php';
require_once __DIR__ . '/../src/model/SchoolClass.php';
require_once __DIR__ . '/../src/model/ClassSchedule.php';
require_once __DIR__ . '/../src/model/Student.php';
require_once __DIR__ . '/../src/model/Lesson.php';
require_once __DIR__ . '/../src/model/Attendance.php';
require_once __DIR__ . '/../src/model/StudentUserRelation.php';
require_once __DIR__ . '/../src/model/User.php';
require_once __DIR__ . '/../src/repository/ClassRepository.php';
require_once __DIR__ . '/../src/repository/StudentRepository.php';
require_once __DIR__ . '/../src/repository/LessonRepository.php';
require_once __DIR__ . '/../src/repository/UserRepository.php';
require_once __DIR__ . '/../src/service/ClassService.php';
require_once __DIR__ . '/../src/service/StudentService.php';
require_once __DIR__ . '/../src/service/LessonService.php';
require_once __DIR__ . '/../src/service/UserService.php';

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function expectDomainException(callable $operation, string $message): void
{
    try {
        $operation();
    } catch (DomainException) {
        return;
    }

    throw new RuntimeException($message);
}

function generateCpf(): string
{
    $cpf = '';

    for ($index = 0; $index < 9; $index++) {
        $cpf .= (string) random_int(0, 9);
    }

    for ($position = 9; $position < 11; $position++) {
        $sum = 0;

        for ($index = 0; $index < $position; $index++) {
            $sum += (int) $cpf[$index] * ($position + 1 - $index);
        }

        $cpf .= (string) ((10 * $sum) % 11 % 10);
    }

    return $cpf;
}

$env = parse_ini_file(__DIR__ . '/../.env');
$pdo = new PDO("mysql:host={$env['DB_HOST']};dbname={$env['DB_NAME']}", $env['DB_USER'], $env['DB_PASSWORD']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->beginTransaction();

try {
    $classes = new ClassRepository($pdo);
    $students = new StudentRepository($pdo);
    $lessons = new LessonRepository($pdo);
    $users = new UserRepository($pdo);
    $classService = new ClassService($classes, $students);
    $studentService = new StudentService($students, $classes, $users);
    $lessonService = new LessonService($lessons, $classes, $students);
    $userService = new UserService($users);
    $suffix = uniqid();
    $parentCpf = generateCpf();
    $secondParentCpf = generateCpf();
    $teacherCpf = generateCpf();

    $class = $classService->create('Teste de Integração', 2026);
    $student = $studentService->create('Aluno de Teste', '2018-01-01', "TEST-{$suffix}", $class->getId());
    $studentWithoutClass = $studentService->create('Aluno Sem Turma', '2018-02-01', "TEST-SEM-TURMA-{$suffix}", null);
    $schedule = $classService->addSchedule($class->getId(), 1, '08:00', '10:00');
    $lesson = $lessonService->create('2026-09-21', 'Planejamento', 'Conteúdo aplicado', $class->getId());

    check(count($classService->students($class->getId())) === 1, 'A turma deveria ter um aluno.');
    check(count($classService->schedules($class->getId())) === 1, 'A turma deveria ter um horário.');
    check($schedule->getClassId() === $class->getId(), 'O horário deveria pertencer à turma criada.');

    $lessonService->registerAttendance($lesson->getId(), $student->getId(), 1);
    check(count($lessonService->attendance($lesson->getId())) === 1, 'A aula deveria ter uma presença.');
    $lessonService->registerAttendance($lesson->getId(), $student->getId(), 2);
    check(count($lessonService->attendance($lesson->getId())) === 1, 'O registro de presença não deve duplicar aluno e aula.');
    check($lessonService->attendance($lesson->getId())[0]->getStatus()->value === 2, 'O status da presença deveria ser atualizado.');

    expectDomainException(
        fn () => $studentService->create('Aluno Duplicado', '2018-03-01', $student->getRegistration(), $class->getId()),
        'Matrícula duplicada deveria ser rejeitada.',
    );
    expectDomainException(
        fn () => $lessonService->registerAttendance($lesson->getId(), $studentWithoutClass->getId(), 1),
        'Aluno de outra turma não deveria receber presença.',
    );
    expectDomainException(
        fn () => $lessonService->registerAttendance($lesson->getId(), $student->getId(), 99),
        'Status de presença inválido deveria ser rejeitado.',
    );
    expectDomainException(
        fn () => $classService->delete($class->getId()),
        'Turma com aula cadastrada não deveria ser excluída.',
    );

    $studentService->update($student->getId(), 'Aluno Atualizado', '2018-01-01', $student->getRegistration());
    $lessonService->update($lesson->getId(), '2026-09-22', 'Planejamento atualizado', 'Conteúdo atualizado', 0);
    $classService->update($class->getId(), 'Turma Atualizada', 2027);
    check($studentService->find($student->getId())->getName() === 'Aluno Atualizado', 'O aluno deveria ser atualizado.');
    check($lessonService->find($lesson->getId())->getStatus() === 0, 'A aula deveria ser atualizada.');
    check($lessonService->find($lesson->getId())->getPlannedContent() === 'Planejamento atualizado', 'O planejamento da aula deveria ser atualizado.');
    check($classService->find($class->getId())->getYear() === 2027, 'A turma deveria ser atualizada.');

    $username = "responsavel-{$suffix}";
    $user = $userService->createAccount($username, 'Responsável de Teste', "responsavel-{$suffix}@example.com", '11999990000', $parentCpf, 'SP', 2, 'senha-segura');
    $secondParent = $userService->createAccount("responsavel-2-{$suffix}", 'Segundo Responsável', "responsavel-2-{$suffix}@example.com", '11955550000', $secondParentCpf, 'SP', 2, 'senha-segura');
    $teacher = $userService->createAccount("professor-{$suffix}", 'Professor de Teste', "professor-{$suffix}@example.com", '11966660000', $teacherCpf, 'SP', 1, 'senha-segura');
    check($user->verifyPassword('senha-segura'), 'A senha do usuário deveria ser protegida.');
    expectDomainException(
        fn () => $userService->createAccount("tipo-invalido-{$suffix}", 'Tipo Inválido', "tipo-invalido-{$suffix}@example.com", '11977770000', '12345678909', 'SP', 99, 'senha-segura'),
        'Tipo de conta inválido deveria ser rejeitado.',
    );
    expectDomainException(
        fn () => $studentService->linkUser($student->getId(), $teacher->getId(), 'Responsável'),
        'Apenas contas de responsável devem ser vinculadas ao aluno.',
    );
    expectDomainException(
        fn () => $studentService->linkUser($student->getId(), $teacher->getId() + 999999, 'Responsável'),
        'Usuário inexistente não deve ser vinculado ao aluno.',
    );
    $studentService->linkUser($student->getId(), $user->getId(), 'Responsável');
    $studentService->linkUser($student->getId(), $secondParent->getId(), 'Responsável');
    $relationCount = $pdo->prepare('SELECT COUNT(*) FROM `STUDENT_has_USER` WHERE `STUDENT_ID` = :student_id AND `USER_ID` = :user_id');
    $relationCount->execute(['student_id' => $student->getId(), 'user_id' => $user->getId()]);
    check((int) $relationCount->fetchColumn() === 1, 'O usuário deveria ser vinculado ao aluno.');

    $userService->editAccount($username, 'Responsável Atualizado', "atualizado-{$suffix}@example.com", '11988880000', '11144477735', 'RJ');
    $userService->changeAccountPassword($username, 'senha-segura', 'nova-senha-segura');
    $updatedUser = $userService->findAccount($username);
    check($updatedUser->getName() === 'Responsável Atualizado', 'O perfil do usuário deveria ser atualizado.');
    check($updatedUser->verifyPassword('nova-senha-segura'), 'A senha do usuário deveria ser atualizada.');
    expectDomainException(
        fn () => $userService->createAccount("duplicado-{$suffix}", 'Duplicado', $updatedUser->getEmail(), '11977770000', '12345678909', 'SP', 2, 'senha-segura'),
        'E-mail de usuário duplicado deveria ser rejeitado.',
    );
    expectDomainException(
        fn () => $userService->createAccount($username, 'Duplicado', "usuario-{$suffix}@example.com", '11977770000', '12345678909', 'SP', 2, 'senha-segura'),
        'Nome de usuário duplicado deveria ser rejeitado.',
    );
    expectDomainException(
        fn () => $userService->createAccount("celular-duplicado-{$suffix}", 'Duplicado', "celular-{$suffix}@example.com", $updatedUser->getCellphone(), '12345678909', 'SP', 2, 'senha-segura'),
        'Celular duplicado deveria ser rejeitado.',
    );
    expectDomainException(
        fn () => $userService->createAccount("cpf-duplicado-{$suffix}", 'Duplicado', "cpf-{$suffix}@example.com", '11977770000', $updatedUser->getCpf(), 'SP', 2, 'senha-segura'),
        'CPF duplicado deveria ser rejeitado.',
    );
    expectDomainException(
        fn () => $userService->changeAccountPassword($username, 'senha-incorreta', 'outra-senha-segura'),
        'Senha atual incorreta deveria ser rejeitada.',
    );

    $userService->deleteAccount($username);
    $relationCount->execute(['student_id' => $student->getId(), 'user_id' => $user->getId()]);
    check((int) $relationCount->fetchColumn() === 0, 'O vínculo deve ser removido ao excluir o usuário.');
    $userService->deleteAccount($teacher->getUsername());
    $lessonService->delete($lesson->getId());
    $studentService->delete($student->getId());
    $relationCount->execute(['student_id' => $student->getId(), 'user_id' => $secondParent->getId()]);
    check((int) $relationCount->fetchColumn() === 0, 'Os vínculos devem ser removidos ao excluir o aluno.');
    $userService->deleteAccount($secondParent->getUsername());
    $studentService->delete($studentWithoutClass->getId());
    $classService->delete($class->getId());
    check($classes->findById($class->getId()) === null, 'A turma deveria ser excluída.');
    echo "Integration test passed\n";
} finally {
    $pdo->rollBack();
}
