<?php

declare(strict_types=1);

namespace Carlos\TechAcademy3\Model;

use Carlos\TechAcademy3\Model\Enum\AttendanceStatus;
use DomainException;

final class Attendance
{
    private ?int $id = null;

    private function __construct(private AttendanceStatus $status, private int $studentId, private int $lessonId)
    {
        if ($studentId <= 0 || $lessonId <= 0) {
            throw new DomainException('Aluno ou aula inválidos.');
        }
    }

    public static function create(AttendanceStatus $status, int $studentId, int $lessonId): self
    {
        return new self($status, $studentId, $lessonId);
    }

    public static function fromDatabase(int $id, AttendanceStatus $status, int $studentId, int $lessonId): self
    {
        $attendance = new self($status, $studentId, $lessonId);
        $attendance->assignId($id);
        return $attendance;
    }

    public function assignId(int $id): void
    {
        if ($id <= 0) { throw new DomainException('ID da presença inválido.'); }
        $this->id = $id;
    }
    public function updateStatus(AttendanceStatus $status): void { $this->status = $status; }
    public function getId(): ?int { return $this->id; }
    public function getStatus(): AttendanceStatus { return $this->status; }
    public function getStudentId(): int { return $this->studentId; }
    public function getLessonId(): int { return $this->lessonId; }
}
