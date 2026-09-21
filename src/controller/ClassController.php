<?php

declare (strict_types = 1);

namespace Carlos\TechAcademy3\Controller;

use Carlos\TechAcademy3\Model\SchoolClass;
use Carlos\TechAcademy3\Service\ClassService;
use DomainException;
use Throwable;

final class ClassController
{
    public function __construct(private ClassService $classService)
    {}
    public function handle(string $method, string $action): void
    {
        try {
            if ($method === 'GET' && $action === 'list') {
                $classes = [];

                foreach ($this->classService->list() as $class) {
                    $classes[] = $this->data($class);
                }

                $this->respond(200, ['data' => $classes]);
                return;
            }
            if ($method === 'GET' && $action === 'show') {
                $id        = $this->id('id');
                $class     = $this->classService->find($id);
                $students  = [];
                $schedules = [];

                foreach ($this->classService->students($id) as $student) {
                    $students[] = ['id' => $student->getId(), 'name' => $student->getName(), 'registration' => $student->getRegistration()];
                }
                foreach ($this->classService->schedules($id) as $schedule) {
                    $schedules[] = ['id' => $schedule->getId(), 'weekday' => $schedule->getWeekday(), 'startTime' => $schedule->getStartTime(), 'endTime' => $schedule->getEndTime()];
                }

                $this->respond(200, ['data' => $this->data($class), 'students' => $students, 'schedules' => $schedules]);
                return;
            }
            if ($method === 'POST' && $action === 'create') {$class = $this->classService->create($this->post('name'), $this->id('year'));
                $this->respond(201, ['data' => $this->data($class)]);return;}
            if ($method === 'POST' && $action === 'edit') {$class = $this->classService->update($this->id('id'), $this->post('name'), $this->id('year'));
                $this->respond(200, ['data' => $this->data($class)]);return;}
            if ($method === 'POST' && $action === 'add-student') {$this->classService->addStudent($this->id('id'), $this->id('studentId'));
                $this->respond(200, ['data' => ['message' => 'Aluno adicionado à turma.']]);return;}
            if ($method === 'POST' && $action === 'remove-student') {$this->classService->removeStudent($this->id('id'), $this->id('studentId'));
                $this->respond(200, ['data' => ['message' => 'Aluno removido da turma.']]);return;}
            if ($method === 'POST' && $action === 'add-schedule') {$schedule = $this->classService->addSchedule($this->id('id'), $this->id('weekday'), $this->post('startTime'), $this->post('endTime'));
                $this->respond(201, ['data' => ['id' => $schedule->getId()]]);return;}
            if ($method === 'POST' && $action === 'remove-schedule') {$this->classService->removeSchedule($this->id('id'), $this->id('scheduleId'));
                $this->respond(200, ['data' => ['message' => 'Horário removido com sucesso.']]);return;}
            if ($method === 'POST' && $action === 'delete') {$this->classService->delete($this->id('id'));
                $this->respond(200, ['data' => ['message' => 'Turma excluída com sucesso.']]);return;}
            $this->respond(400, ['error' => ['message' => 'Ação ou método HTTP inválido.']]);
        } catch (DomainException $exception) {$this->respond(str_contains($exception->getMessage(), 'não encontrad') ? 404 : 400, ['error' => ['message' => $exception->getMessage()]]);} catch (Throwable $exception) {error_log($exception->getMessage());
            $this->respond(500, ['error' => ['message' => 'Erro interno do servidor.']]);}
    }
    private function data(SchoolClass $class): array
    {return ['id' => $class->getId(), 'name' => $class->getName(), 'year' => $class->getYear()];}
    private function post(string $field): string
    {return is_string($_POST[$field] ?? null) ? $_POST[$field] : '';}
    private function id(string $field): int
    {return (int) $this->post($field) ?: (int) ($_GET[$field] ?? 0);}
    private function respond(int $status, array $body): void
    {http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);}
}
