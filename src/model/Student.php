<?php

declare(strict_types=1);

namespace Carlos\TechAcademy3\Model;

use DateTimeImmutable;
use DomainException;

final class Student
{
    private ?int $id = null;
    private ?int $classId = null;

    private function __construct(private string $name, private string $birthDate, private string $registration)
    {
        $this->name = self::required($name, 'Nome do aluno');
        $this->birthDate = self::date($birthDate);
        $this->registration = self::required($registration, 'Matrícula');
    }

    public static function create(string $name, string $birthDate, string $registration): self
    {
        return new self($name, $birthDate, $registration);
    }

    public static function fromDatabase(int $id, string $name, string $birthDate, string $registration, ?int $classId): self
    {
        $student = new self($name, $birthDate, $registration);
        $student->assignId($id);

        if ($classId !== null) {
            $student->assignClass($classId);
        }

        return $student;
    }

    public function assignId(int $id): void
    {
        if ($id <= 0) {
            throw new DomainException('ID do aluno inválido.');
        }

        $this->id = $id;
    }

    public function update(string $name, string $birthDate, string $registration): void
    {
        $this->name = self::required($name, 'Nome do aluno');
        $this->birthDate = self::date($birthDate);
        $this->registration = self::required($registration, 'Matrícula');
    }

    public function assignClass(int $classId): void
    {
        if ($classId <= 0) {
            throw new DomainException('Turma inválida.');
        }

        $this->classId = $classId;
    }

    public function removeClass(): void
    {
        $this->classId = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBirthDate(): string
    {
        return $this->birthDate;
    }

    public function getRegistration(): string
    {
        return $this->registration;
    }

    public function getClassId(): ?int
    {
        return $this->classId;
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
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);

        if ($date === false || $date->format('Y-m-d') !== $value) {
            throw new DomainException('Data de nascimento inválida.');
        }

        return $value;
    }
}
