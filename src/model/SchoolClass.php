<?php

declare(strict_types=1);

namespace Carlos\TechAcademy3\Model;

use DomainException;

final class SchoolClass
{
    private ?int $id = null;

    private function __construct(private string $name, private int $year)
    {
        $this->name = self::required($name, 'Nome da turma');

        if ($year < 2000 || $year > 2100) {
            throw new DomainException('Ano da turma inválido.');
        }
    }

    public static function create(string $name, int $year): self
    {
        return new self($name, $year);
    }

    public static function fromDatabase(int $id, string $name, int $year): self
    {
        $class = new self($name, $year);
        $class->assignId($id);

        return $class;
    }

    public function assignId(int $id): void
    {
        if ($id <= 0) {
            throw new DomainException('ID da turma inválido.');
        }

        $this->id = $id;
    }

    public function update(string $name, int $year): void
    {
        $this->name = self::required($name, 'Nome da turma');

        if ($year < 2000 || $year > 2100) {
            throw new DomainException('Ano da turma inválido.');
        }

        $this->year = $year;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    private static function required(string $value, string $field): string
    {
        $value = trim($value);

        if ($value === '') {
            throw new DomainException("{$field} é obrigatório.");
        }

        return $value;
    }
}
