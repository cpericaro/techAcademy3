<?php

declare(strict_types=1);

namespace Carlos\TechAcademy3\Model;

use DateTimeImmutable;
use DomainException;

final class ClassSchedule
{
    private ?int $id = null;

    private function __construct(
        private int $weekday,
        private string $startTime,
        private string $endTime,
        private int $classId,
    ) {
        if ($weekday < 1 || $weekday > 7 || $classId <= 0) {
            throw new DomainException('Dia da semana ou turma inválidos.');
        }
        $this->startTime = self::time($startTime);
        $this->endTime = self::time($endTime);
        if ($this->startTime >= $this->endTime) {
            throw new DomainException('O horário final deve ser posterior ao inicial.');
        }
    }

    public static function create(int $weekday, string $startTime, string $endTime, int $classId): self
    {
        return new self($weekday, $startTime, $endTime, $classId);
    }

    public static function fromDatabase(int $id, int $weekday, string $startTime, string $endTime, int $classId): self
    {
        $schedule = new self($weekday, $startTime, $endTime, $classId);
        $schedule->assignId($id);
        return $schedule;
    }

    public function assignId(int $id): void
    {
        if ($id <= 0) { throw new DomainException('ID do horário inválido.'); }
        $this->id = $id;
    }
    public function update(int $weekday, string $startTime, string $endTime): void
    {
        $updated = new self($weekday, $startTime, $endTime, $this->classId);
        $this->weekday = $updated->weekday;
        $this->startTime = $updated->startTime;
        $this->endTime = $updated->endTime;
    }
    public function getId(): ?int { return $this->id; }
    public function getWeekday(): int { return $this->weekday; }
    public function getStartTime(): string { return $this->startTime; }
    public function getEndTime(): string { return $this->endTime; }
    public function getClassId(): int { return $this->classId; }

    private static function time(string $value): string
    {
        $time = DateTimeImmutable::createFromFormat('!H:i', $value)
            ?: DateTimeImmutable::createFromFormat('!H:i:s', $value);
        if ($time === false) { throw new DomainException('Horário inválido.'); }
        return $time->format('H:i:s');
    }
}
