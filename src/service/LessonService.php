<?php

declare(strict_types=1);

namespace Carlos\TechAcademy3\Service;

use Carlos\TechAcademy3\Model\Attendance;
use Carlos\TechAcademy3\Model\Enum\AttendanceStatus;
use Carlos\TechAcademy3\Model\Lesson;
use Carlos\TechAcademy3\Repository\ClassRepository;
use Carlos\TechAcademy3\Repository\LessonRepository;
use Carlos\TechAcademy3\Repository\StudentRepository;
use DomainException;

final class LessonService
{
    public function __construct(
        private LessonRepository $lessonRepository,
        private ClassRepository $classRepository,
        private StudentRepository $studentRepository,
    ) {}

    public function create(string $date, string $plannedContent, string $content, int $classId): Lesson
    {
        $this->ensureClass($classId);
        $lesson = Lesson::create($date, $plannedContent, $content, $classId);
        $this->lessonRepository->create($lesson);

        return $lesson;
    }

    public function find(int $id): Lesson
    {
        $lesson = $this->lessonRepository->findById($id);

        if ($lesson === null) {
            throw new DomainException('Aula não encontrada.');
        }

        return $lesson;
    }

    public function list(): array
    {
        return $this->lessonRepository->findAll();
    }

    public function update(int $id, string $date, string $plannedContent, string $content, int $status): Lesson
    {
        $lesson = $this->find($id);
        $lesson->update($date, $plannedContent, $content, $status);
        $this->lessonRepository->update($lesson);

        return $lesson;
    }

    public function delete(int $id): void
    {
        $this->lessonRepository->delete($this->find($id));
    }

    public function registerAttendance(int $lessonId, int $studentId, int $status): void
    {
        $lesson = $this->find($lessonId);
        $student = $this->studentRepository->findById($studentId);

        if ($student === null) {
            throw new DomainException('Aluno não encontrado.');
        }
        if ($student->getClassId() !== $lesson->getClassId()) {
            throw new DomainException('Aluno não pertence à turma desta aula.');
        }

        try {
            $attendanceStatus = AttendanceStatus::from($status);
        } catch (\ValueError) {
            throw new DomainException('Status de presença inválido.');
        }

        $attendance = Attendance::create($attendanceStatus, $studentId, $lessonId);
        $this->lessonRepository->saveAttendance($attendance);
    }

    public function attendance(int $lessonId): array
    {
        $this->find($lessonId);

        return $this->lessonRepository->findAttendanceByLessonId($lessonId);
    }

    public function attendanceByStudent(int $studentId): array
    {
        if ($this->studentRepository->findById($studentId) === null) {
            throw new DomainException('Aluno não encontrado.');
        }

        return $this->lessonRepository->findAttendanceByStudentId($studentId);
    }

    private function ensureClass(int $classId): void
    {
        if ($this->classRepository->findById($classId) === null) {
            throw new DomainException('Turma não encontrada.');
        }
    }
}
