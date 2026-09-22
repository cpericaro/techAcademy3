<?php

declare(strict_types=1);

namespace Carlos\TechAcademy3\Controller;

use Carlos\TechAcademy3\Model\Enum\AccountType;
use Carlos\TechAcademy3\Model\Lesson;
use Carlos\TechAcademy3\Service\AuthenticationService;
use Carlos\TechAcademy3\Service\LessonService;
use DomainException;
use Throwable;

final class LessonController
{
    public function __construct(
        private LessonService $lessonService,
        private AuthenticationService $authenticationService,
    ) {
    }

    public function handle(string $method, string $action): void
    {
        try {
            $this->authenticationService->requireAccountType(AccountType::ADMIN, AccountType::TEACHER);

            if ($method === 'GET' && $action === 'list') {
                $lessons = [];

                foreach ($this->lessonService->list() as $lesson) {
                    $lessons[] = $this->data($lesson);
                }

                $this->respond(200, ['data' => $lessons]);
                return;
            }
            if ($method === 'GET' && $action === 'show') {
                $lesson = $this->lessonService->find($this->id('id'));
                $attendance = [];

                foreach ($this->lessonService->attendance($lesson->getId()) as $item) {
                    $attendance[] = [
                        'id' => $item->getId(),
                        'studentId' => $item->getStudentId(),
                        'status' => $item->getStatus()->value,
                    ];
                }

                $this->respond(200, ['data' => $this->data($lesson), 'attendance' => $attendance]);
                return;
            }

            if ($method === 'POST' && $action === 'create') {
                $lesson = $this->lessonService->create(
                    $this->post('lessonDate'),
                    $this->post('plannedContent'),
                    $this->post('content'),
                    $this->id('classId'),
                );

                $this->respond(201, ['data' => $this->data($lesson)]);
                return;
            }

            if ($method === 'POST' && $action === 'edit') {
                $lesson = $this->lessonService->update(
                    $this->id('id'),
                    $this->post('lessonDate'),
                    $this->post('plannedContent'),
                    $this->post('content'),
                    $this->id('status'),
                );

                $this->respond(200, ['data' => $this->data($lesson)]);
                return;
            }

            if ($method === 'POST' && $action === 'register-attendance') {
                $this->lessonService->registerAttendance(
                    $this->id('id'),
                    $this->id('studentId'),
                    $this->id('status'),
                );

                $this->respond(200, ['data' => ['message' => 'Presença registrada com sucesso.']]);
                return;
            }

            if ($method === 'POST' && $action === 'delete') {
                $this->lessonService->delete($this->id('id'));

                $this->respond(200, ['data' => ['message' => 'Aula excluída com sucesso.']]);
                return;
            }

            $this->respond(400, ['error' => ['message' => 'Ação ou método HTTP inválido.']]);
        } catch (DomainException $exception) {
            $this->respond($this->status($exception), ['error' => ['message' => $exception->getMessage()]]);
        } catch (Throwable $exception) {
            error_log($exception->getMessage());

            $this->respond(500, ['error' => ['message' => 'Erro interno do servidor.']]);
        }
    }

    private function data(Lesson $lesson): array
    {
        return [
            'id' => $lesson->getId(),
            'lessonDate' => $lesson->getLessonDate(),
            'plannedContent' => $lesson->getPlannedContent(),
            'content' => $lesson->getContent(),
            'status' => $lesson->getStatus(),
            'classId' => $lesson->getClassId(),
        ];
    }

    private function post(string $field): string
    {
        return is_string($_POST[$field] ?? null) ? $_POST[$field] : '';
    }

    private function id(string $field): int
    {
        return (int) $this->post($field) ?: (int) ($_GET[$field] ?? 0);
    }

    private function status(DomainException $exception): int
    {
        return match ($exception->getMessage()) {
            'Autenticação necessária.' => 401,
            'Acesso não autorizado.' => 403,
            default => str_contains($exception->getMessage(), 'não encontrad') ? 404 : 400,
        };
    }

    private function respond(int $status, array $body): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
}
