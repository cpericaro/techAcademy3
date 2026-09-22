<?php

declare(strict_types=1);

namespace Carlos\TechAcademy3\Service;

use Carlos\TechAcademy3\Model\Student;
use Carlos\TechAcademy3\Model\StudentUserRelation;
use Carlos\TechAcademy3\Repository\ClassRepository;
use Carlos\TechAcademy3\Repository\StudentRepository;
use DomainException;

final class StudentService
{
    public function __construct(
        private StudentRepository $studentRepository,
        private ClassRepository $classRepository,
    ) {}

    public function create(string $name, string $birthDate, string $registration, ?int $classId): Student
    {
        $student = Student::create($name, $birthDate, $registration);
        $this->ensureRegistrationAvailable($student);

        if ($classId !== null) {
            $student->assignClass($this->classId($classId));
        }

        $this->studentRepository->create($student);

        return $student;
    }

    public function find(int $id): Student
    {
        $student = $this->studentRepository->findById($id);

        if ($student === null) {
            throw new DomainException('Aluno não encontrado.');
        }

        return $student;
    }

    public function list(): array
    {
        return $this->studentRepository->findAll();
    }

    public function listByUser(int $userId): array
    {
        return $this->studentRepository->findByUserId($userId);
    }

    public function isLinkedToUser(int $studentId, int $userId): bool
    {
        return $this->studentRepository->isLinkedToUser($studentId, $userId);
    }

    public function update(int $id, string $name, string $birthDate, string $registration): Student
    {
        $student = $this->find($id);
        $previousRegistration = $student->getRegistration();
        $student->update($name, $birthDate, $registration);

        if ($student->getRegistration() !== $previousRegistration) {
            $this->ensureRegistrationAvailable($student);
        }

        $this->studentRepository->update($student);

        return $student;
    }

    public function assignClass(int $studentId, int $classId): Student
    {
        $student = $this->find($studentId);
        $student->assignClass($this->classId($classId));
        $this->studentRepository->update($student);

        return $student;
    }

    public function removeClass(int $studentId): Student
    {
        $student = $this->find($studentId);
        $student->removeClass();
        $this->studentRepository->update($student);

        return $student;
    }

    public function delete(int $id): void
    {
        $this->studentRepository->delete($this->find($id));
    }

    public function linkUser(int $studentId, int $userId, string $relationship): void
    {
        $this->find($studentId);
        $relation = new StudentUserRelation($studentId, $userId, $relationship);
        $this->studentRepository->saveUserRelation($relation);
    }

    public function unlinkUser(int $studentId, int $userId): void
    {
        $this->find($studentId);
        $this->studentRepository->removeUserRelation($studentId, $userId);
    }

    private function classId(int $classId): int
    {
        if ($this->classRepository->findById($classId) === null) {
            throw new DomainException('Turma não encontrada.');
        }

        return $classId;
    }

    private function ensureRegistrationAvailable(Student $student): void
    {
        if ($this->studentRepository->findByRegistration($student->getRegistration()) !== null) {
            throw new DomainException('Matrícula indisponível.');
        }
    }
}
