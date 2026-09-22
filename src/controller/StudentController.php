<?php

declare (strict_types = 1);

namespace Carlos\TechAcademy3\Controller;

use Carlos\TechAcademy3\Model\Enum\AccountType;
use Carlos\TechAcademy3\Model\Student;
use Carlos\TechAcademy3\Service\AuthenticationService;
use Carlos\TechAcademy3\Service\LessonService;
use Carlos\TechAcademy3\Service\StudentService;
use DomainException;
use Throwable;

final class StudentController
{
    public function __construct(
        private StudentService $studentService,
        private LessonService $lessonService,
        private AuthenticationService $authenticationService,
    ) {}
    public function handle(string $method, string $action): void
    {
        try {
            $authenticatedUser = $this->authenticationService->requireAuthenticatedUser();

            if ($method === 'GET' && $action === 'my-children') {
                $user     = $this->authenticationService->requireAccountType(AccountType::PARENT);
                $students = [];

                foreach ($this->studentService->listByUser((int) $user->getId()) as $student) {
                    $students[] = $this->data($student);
                }

                $this->respond(200, ['data' => $students]);
                return;
            }

            if ($method === 'GET' && $action === 'show' && $authenticatedUser->getAccountType() === AccountType::PARENT) {
                $student = $this->studentService->find($this->id('id'));

                if (! $this->studentService->isLinkedToUser((int) $student->getId(), (int) $authenticatedUser->getId())) {
                    throw new DomainException('Acesso não autorizado.');
                }

                $attendance = [];

                foreach ($this->lessonService->attendanceByStudent($student->getId()) as $item) {
                    $attendance[] = ['lessonId' => $item->getLessonId(), 'status' => $item->getStatus()->value];
                }

                $this->respond(200, ['data' => $this->data($student), 'attendance' => $attendance]);
                return;
            }

            $this->authenticationService->requireAccountType(AccountType::ADMIN, AccountType::TEACHER);

            if ($method === 'GET' && $action === 'list') {
                $students = [];

                foreach ($this->studentService->list() as $student) {
                    $students[] = $this->data($student);
                }

                $this->respond(200, ['data' => $students]);
                return;
            }
            if ($method === 'GET' && $action === 'show') {
                $student    = $this->studentService->find($this->id('id'));
                $attendance = [];

                foreach ($this->lessonService->attendanceByStudent($student->getId()) as $item) {
                    $attendance[] = ['lessonId' => $item->getLessonId(), 'status' => $item->getStatus()->value];
                }

                $this->respond(200, ['data' => $this->data($student), 'attendance' => $attendance]);
                return;
            }
            if ($method === 'POST' && $action === 'create') {$student = $this->studentService->create($this->post('name'), $this->post('birthDate'), $this->post('registration'), $this->optionalId('classId'));
                $this->respond(201, ['data' => $this->data($student)]);return;}
            if ($method === 'POST' && $action === 'edit') {$student = $this->studentService->update($this->id('id'), $this->post('name'), $this->post('birthDate'), $this->post('registration'));
                $this->respond(200, ['data' => $this->data($student)]);return;}
            if ($method === 'POST' && $action === 'assign-class') {$student = $this->studentService->assignClass($this->id('id'), $this->id('classId'));
                $this->respond(200, ['data' => $this->data($student)]);return;}
            if ($method === 'POST' && $action === 'remove-class') {$student = $this->studentService->removeClass($this->id('id'));
                $this->respond(200, ['data' => $this->data($student)]);return;}
            if ($method === 'POST' && $action === 'link-user') {$this->studentService->linkUser($this->id('id'), $this->id('userId'), $this->post('relationship'));
                $this->respond(200, ['data' => ['message' => 'Responsável vinculado com sucesso.']]);return;}
            if ($method === 'POST' && $action === 'unlink-user') {$this->studentService->unlinkUser($this->id('id'), $this->id('userId'));
                $this->respond(200, ['data' => ['message' => 'Responsável removido com sucesso.']]);return;}
            if ($method === 'POST' && $action === 'delete') {$this->studentService->delete($this->id('id'));
                $this->respond(200, ['data' => ['message' => 'Aluno excluído com sucesso.']]);return;}
            $this->respond(400, ['error' => ['message' => 'Ação ou método HTTP inválido.']]);
        } catch (DomainException $exception) {$this->respond($this->status($exception), ['error' => ['message' => $exception->getMessage()]]);} catch (Throwable $exception) {error_log($exception->getMessage());
            $this->respond(500, ['error' => ['message' => 'Erro interno do servidor.']]);}
    }
    private function data(Student $student): array
    {return ['id' => $student->getId(), 'name' => $student->getName(), 'birthDate' => $student->getBirthDate(), 'registration' => $student->getRegistration(), 'classId' => $student->getClassId()];}
    private function post(string $field): string
    {return is_string($_POST[$field] ?? null) ? $_POST[$field] : '';}
    private function id(string $field): int
    {return (int) $this->post($field ?: 'id') ?: (int) ($_GET[$field] ?? 0);}
    private function optionalId(string $field): ?int
    {$id = $this->id($field);return $id > 0 ? $id : null;}
    private function status(DomainException $exception): int
    {return match ($exception->getMessage()) {'Autenticação necessária.' => 401, 'Acesso não autorizado.' => 403,     default => str_contains($exception->getMessage(), 'não encontrad') ? 404 : 400};}
    private function respond(int $status, array $body): void
    {http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);}
}
