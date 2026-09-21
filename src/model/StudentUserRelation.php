<?php

declare(strict_types=1);

namespace Carlos\TechAcademy3\Model;

use DomainException;

final class StudentUserRelation
{
    public function __construct(
        private int $studentId,
        private int $userId,
        private string $relationship,
    ) {
        if ($studentId <= 0 || $userId <= 0) {
            throw new DomainException('Aluno ou usuário inválido.');
        }

        $this->relationship = trim($relationship);

        if ($this->relationship === '') {
            throw new DomainException('Parentesco é obrigatório.');
        }
    }

    public function getStudentId(): int
    {
        return $this->studentId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getRelationship(): string
    {
        return $this->relationship;
    }
}
