<?php

declare(strict_types=1);

namespace Carlos\TechAcademy3\Service;

use Carlos\TechAcademy3\Model\ClassSchedule;
use Carlos\TechAcademy3\Model\SchoolClass;
use Carlos\TechAcademy3\Repository\ClassRepository;
use Carlos\TechAcademy3\Repository\StudentRepository;
use DomainException;

final class ClassService
{
    public function __construct(
        private ClassRepository $classRepository,
        private StudentRepository $studentRepository,
    ) {}

    public function create(string $name, int $year): SchoolClass
    {
        $class = SchoolClass::create($name, $year);
        $this->classRepository->create($class);

        return $class;
    }

    public function find(int $id): SchoolClass
    {
        $class = $this->classRepository->findById($id);

        if ($class === null) {
            throw new DomainException('Turma não encontrada.');
        }

        return $class;
    }

    public function list(): array
    {
        return $this->classRepository->findAll();
    }

    public function update(int $id, string $name, int $year): SchoolClass
    {
        $class = $this->find($id);
        $class->update($name, $year);
        $this->classRepository->update($class);

        return $class;
    }

    public function delete(int $id): void
    {
        $class = $this->find($id);

        if ($this->classRepository->hasLessons($id)) {
            throw new DomainException('Não é possível excluir turma com aulas cadastradas.');
        }

        $this->classRepository->delete($class);
    }

    public function addStudent(int $classId, int $studentId): void
    {
        $this->find($classId);
        $student = $this->studentRepository->findById($studentId);

        if ($student === null) {
            throw new DomainException('Aluno não encontrado.');
        }

        $student->assignClass($classId);
        $this->studentRepository->update($student);
    }

    public function removeStudent(int $classId, int $studentId): void
    {
        $this->find($classId);
        $student = $this->studentRepository->findById($studentId);

        if ($student === null || $student->getClassId() !== $classId) {
            throw new DomainException('Aluno não pertence à turma.');
        }

        $student->removeClass();
        $this->studentRepository->update($student);
    }

    public function addSchedule(int $classId, int $weekday, string $startTime, string $endTime): ClassSchedule
    {
        $this->find($classId);
        $schedule = ClassSchedule::create($weekday, $startTime, $endTime, $classId);
        $this->classRepository->createSchedule($schedule);

        return $schedule;
    }

    public function schedules(int $classId): array
    {
        $this->find($classId);

        return $this->classRepository->findSchedulesByClassId($classId);
    }

    public function removeSchedule(int $classId, int $scheduleId): void
    {
        $this->find($classId);
        $this->classRepository->deleteSchedule($scheduleId, $classId);
    }

    public function students(int $classId): array
    {
        $this->find($classId);

        return $this->studentRepository->findByClassId($classId);
    }
}
