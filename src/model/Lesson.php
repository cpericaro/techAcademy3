<?php

declare(strict_types=1);

namespace Carlos\TechAcademy3\Model;

use Carbon\CarbonImmutable;
use DomainException;

final class Lesson
{
    private ?int $id = null;

    private function __construct(
        private string $lessonDate,
        private string $plannedContent,
        private string $content,
        private int $classId,
        private int $status = 1,
    ) {
        $this->lessonDate = self::date($lessonDate);
        $this->plannedContent = self::required($plannedContent, 'Conteúdo planejado');
        $this->content = trim($content);
        $this->classId = self::id($classId, 'Turma');

        if (!in_array($status, [0, 1], true)) {
            throw new DomainException('Status da aula inválido.');
        }
    }

    public static function create(string $lessonDate, string $plannedContent, string $content, int $classId): self
    {
        return new self($lessonDate, $plannedContent, $content, $classId);
    }

    public static function fromDatabase(int $id, string $lessonDate, string $plannedContent, string $content, int $status, int $classId): self
    {
        $lesson = new self($lessonDate, $plannedContent, $content, $classId, $status);
        $lesson->assignId($id);

        return $lesson;
    }

    public function assignId(int $id): void
    {
        $this->id = self::id($id, 'ID da aula');
    }

    public function update(string $lessonDate, string $plannedContent, string $content, int $status): void
    {
        $this->lessonDate = self::date($lessonDate);
        $this->plannedContent = self::required($plannedContent, 'Conteúdo planejado');
        $this->content = trim($content);

        if (!in_array($status, [0, 1], true)) {
            throw new DomainException('Status da aula inválido.');
        }

        $this->status = $status;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLessonDate(): string
    {
        return $this->lessonDate;
    }

    public function getPlannedContent(): string
    {
        return $this->plannedContent;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getClassId(): int
    {
        return $this->classId;
    }

    private static function id(int $id, string $field): int
    {
        if ($id <= 0) {
            throw new DomainException("{$field} inválido.");
        }

        return $id;
    }

    private static function required(string $value, string $field): string
    {
        $value = trim($value);

        if ($value === '') {
            throw new DomainException("{$field} é obrigatório.");
        }

        return $value;
    }

    private static function date(string $value): string
    {
        try {
            $date = CarbonImmutable::createFromFormat('!Y-m-d', $value);
        } catch (\InvalidArgumentException) {
            throw new DomainException('Data da aula inválida.');
        }

        if ($date === null || $date->format('Y-m-d') !== $value) {
            throw new DomainException('Data da aula inválida.');
        }

        return $value;
    }
}
