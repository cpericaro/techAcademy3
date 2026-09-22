<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Carlos\TechAcademy3\Model\Attendance;
use Carlos\TechAcademy3\Model\ClassSchedule;
use Carlos\TechAcademy3\Model\Enum\AccountType;
use Carlos\TechAcademy3\Model\Enum\AttendanceStatus;
use Carlos\TechAcademy3\Model\Lesson;
use Carlos\TechAcademy3\Model\SchoolClass;
use Carlos\TechAcademy3\Model\Student;
use Carlos\TechAcademy3\Model\StudentUserRelation;
use Carlos\TechAcademy3\Model\User;

require_once __DIR__ . '/../src/model/enum/AttendanceStatus.php';
require_once __DIR__ . '/../src/model/enum/AccountType.php';
require_once __DIR__ . '/../src/model/SchoolClass.php';
require_once __DIR__ . '/../src/model/Student.php';
require_once __DIR__ . '/../src/model/Lesson.php';
require_once __DIR__ . '/../src/model/Attendance.php';
require_once __DIR__ . '/../src/model/ClassSchedule.php';
require_once __DIR__ . '/../src/model/StudentUserRelation.php';
require_once __DIR__ . '/../src/model/User.php';

function assertSameValue(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        throw new RuntimeException($message);
    }
}

function assertTrue(bool $condition, string $message): void
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

$class = SchoolClass::create(' Turma Infantil ', 2026);
$class->assignId(1);
$class->update('Turma Infantil A', 2027);
assertSameValue('Turma Infantil A', $class->getName(), 'A turma deve atualizar o nome.');
assertSameValue(2027, $class->getYear(), 'A turma deve atualizar o ano.');

$student = Student::create('Ana Silva', '2018-05-12', 'MAT-001');
$student->assignId(1);
$student->assignClass(1);
$student->update('Ana Souza', '2018-05-12', 'MAT-002');
assertSameValue('Ana Souza', $student->getName(), 'O aluno deve atualizar o nome.');
assertSameValue(1, $student->getClassId(), 'O aluno deve manter a turma ao ser atualizado.');
$student->removeClass();
assertSameValue(null, $student->getClassId(), 'O aluno deve poder ser removido da turma.');

$lesson = Lesson::create('2026-09-21', 'Pintura', 'Mistura de cores', 1);
$lesson->assignId(1);
$lesson->update('2026-09-22', 'Desenho', 'Traços e formas', 0);
assertSameValue('2026-09-22', $lesson->getLessonDate(), 'A aula deve atualizar a data.');
assertSameValue(0, $lesson->getStatus(), 'A aula deve atualizar o status.');

$attendance = Attendance::create(AttendanceStatus::PRESENT, 1, 1);
$attendance->assignId(1);
$attendance->updateStatus(AttendanceStatus::ABSENT);
assertSameValue(AttendanceStatus::ABSENT, $attendance->getStatus(), 'A presença deve atualizar o status.');

$schedule = ClassSchedule::create(1, '08:00', '10:00', 1);
$schedule->assignId(1);
$schedule->update(2, '09:00', '11:00');
assertSameValue(2, $schedule->getWeekday(), 'O horário deve atualizar o dia da semana.');
assertSameValue('09:00:00', $schedule->getStartTime(), 'O horário deve normalizar os segundos.');

$relation = new StudentUserRelation(1, 1, ' Responsável ');
assertSameValue('Responsável', $relation->getRelationship(), 'O parentesco deve remover espaços excedentes.');

$user = User::register('ana.souza', 'Ana Souza', 'ANA@EXAMPLE.COM', '(11) 99999-0000', '529.982.247-25', 'sp', AccountType::PARENT, 'senha-segura');
$user->assignId(1);
assertSameValue('ana@example.com', $user->getEmail(), 'O e-mail deve ser normalizado.');
assertSameValue('11999990000', $user->getCellphone(), 'O celular deve ser normalizado.');
assertSameValue('52998224725', $user->getCpf(), 'O CPF deve ser normalizado.');
assertSameValue('SP', $user->getUf(), 'A UF deve ser normalizada.');
assertTrue($user->verifyPassword('senha-segura'), 'A senha cadastrada deve ser validada.');
$user->updateProfile('Ana Souza Silva', 'ana.silva@example.com', '(11) 98888-0000', '111.444.777-35', 'rj');
$user->changePassword('nova-senha-segura');
assertSameValue('Ana Souza Silva', $user->getName(), 'O perfil deve atualizar o nome.');
assertSameValue('RJ', $user->getUf(), 'O perfil deve atualizar a UF.');
assertTrue($user->verifyPassword('nova-senha-segura'), 'A senha deve ser alterada.');
assertTrue(!$user->verifyPassword('senha-segura'), 'A senha anterior não deve continuar válida.');

expectDomainException(fn () => SchoolClass::create('', 2026), 'Turma sem nome deve ser rejeitada.');
expectDomainException(fn () => SchoolClass::create('Turma', 1999), 'Ano de turma inválido deve ser rejeitado.');
expectDomainException(fn () => Student::create('', '2018-05-12', 'MAT-001'), 'Aluno sem nome deve ser rejeitado.');
expectDomainException(fn () => Student::create('Ana', '2018-02-30', 'MAT-001'), 'Data de nascimento inválida deve ser rejeitada.');
expectDomainException(fn () => $student->assignClass(0), 'Turma inválida deve ser rejeitada para o aluno.');
expectDomainException(fn () => Lesson::create('2026-02-30', 'Pintura', 'Conteúdo', 1), 'Data de aula inválida deve ser rejeitada.');
expectDomainException(fn () => Lesson::create('2026-09-21', '', 'Conteúdo', 1), 'Aula sem conteúdo planejado deve ser rejeitada.');
expectDomainException(fn () => $lesson->update('2026-09-22', 'Desenho', 'Conteúdo', 2), 'Status de aula inválido deve ser rejeitado.');
expectDomainException(fn () => Attendance::create(AttendanceStatus::PRESENT, 0, 1), 'Presença sem aluno válido deve ser rejeitada.');
expectDomainException(fn () => ClassSchedule::create(0, '08:00', '10:00', 1), 'Dia da semana inválido deve ser rejeitado.');
expectDomainException(fn () => ClassSchedule::create(1, '10:00', '08:00', 1), 'Horário final anterior deve ser rejeitado.');
expectDomainException(fn () => ClassSchedule::create(1, 'invalido', '10:00', 1), 'Formato de horário inválido deve ser rejeitado.');
expectDomainException(fn () => new StudentUserRelation(0, 1, 'Responsável'), 'Relação sem aluno válido deve ser rejeitada.');
expectDomainException(fn () => new StudentUserRelation(1, 1, ' '), 'Relação sem parentesco deve ser rejeitada.');
expectDomainException(fn () => User::register('', 'Ana', 'ana@example.com', '11999990000', '52998224725', 'SP', AccountType::PARENT, 'senha-segura'), 'Usuário sem nome deve ser rejeitado.');
expectDomainException(fn () => User::register('ana', 'Ana', 'email-invalido', '11999990000', '52998224725', 'SP', AccountType::PARENT, 'senha-segura'), 'E-mail inválido deve ser rejeitado.');
expectDomainException(fn () => User::register('ana', 'Ana', 'ana@example.com', '1199', '52998224725', 'SP', AccountType::PARENT, 'senha-segura'), 'Celular inválido deve ser rejeitado.');
expectDomainException(fn () => User::register('ana', 'Ana', 'ana@example.com', '11999990000', '11111111111', 'SP', AccountType::PARENT, 'senha-segura'), 'CPF inválido deve ser rejeitado.');
expectDomainException(fn () => User::register('ana', 'Ana', 'ana@example.com', '11999990000', '52998224725', 'XX', AccountType::PARENT, 'senha-segura'), 'UF inválida deve ser rejeitada.');
expectDomainException(fn () => User::register('ana', 'Ana', 'ana@example.com', '11999990000', '52998224725', 'SP', AccountType::PARENT, 'curta'), 'Senha curta deve ser rejeitada.');

echo "Model tests passed\n";
