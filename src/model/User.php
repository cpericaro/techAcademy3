<?php

declare(strict_types=1);

namespace Carlos\TechAcademy3\Model;

use Carlos\TechAcademy3\Model\Enum\AccountType;
use DomainException;
use RuntimeException;

final class User
{
    //assim id não vem null pelo constructor
    private ?int $id = null;
    private string $passwordHash;

    private function __construct(
        private string $username,
        private string $name,
        private string $email,
        private string $cellphone,
        private string $cpf,
        private string $uf,
        private AccountType $accountType,
        string $passwordHash,
    ) {
        $this->username = $this->validateRequired($username, 'Nome de usuário');
        $this->name = $this->validateRequired($name, 'Nome');
        $this->email = $this->validateEmail($email);
        $this->cellphone = $this->normalizeCellphone($cellphone);
        $this->cpf = $this->normalizeCpf($cpf);
        $this->uf = $this->normalizeUf($uf);
        $this->passwordHash = self::validatePasswordHash($passwordHash);
    }

    public static function register(
        string $username,
        string $name,
        string $email,
        string $cellphone,
        string $cpf,
        string $uf,
        AccountType $accountType,
        string $password,
    ): self {
        return new self(
            $username,
            $name,
            $email,
            $cellphone,
            $cpf,
            $uf,
            $accountType,
            self::hashPassword($password),
        );
    }

    public static function fromDatabase(
        int $id,
        string $username,
        string $name,
        string $email,
        string $cellphone,
        string $cpf,
        string $uf,
        AccountType $accountType,
        string $passwordHash,
    ): self {
        $user = new self(
            $username,
            $name,
            $email,
            $cellphone,
            $cpf,
            $uf,
            $accountType,
            $passwordHash,
        );
        $user->assignId($id);

        return $user;
    }

    public function assignId(int $id): void
    {
        if ($id <= 0) {
            throw new DomainException('ID de usuário inválido.');
        }

        $this->id = $id;
    }

    public function changePassword(string $password): void
    {
        if (strlen($password) < 8) {
            throw new DomainException('A senha deve ter ao menos 8 caracteres.');
        }

        $this->passwordHash = self::hashPassword($password);
    }

    // getters e setters

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->passwordHash);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCellphone(): string
    {
        return $this->cellphone;
    }

    public function getCpf(): string
    {
        return $this->cpf;
    }

    public function getUf(): string
    {
        return $this->uf;
    }

    public function getAccountType(): AccountType
    {
        return $this->accountType;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    private function validateRequired(string $value, string $field): string
    {
        $value = trim($value);

        if ($value === '') {
            throw new DomainException("{$field} é obrigatório.");
        }

        return $value;
    }

    private function validateEmail(string $email): string
    {
        $email = strtolower(trim($email));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new DomainException('E-mail inválido.');
        }

        return $email;
    }

    private function normalizeCellphone(string $cellphone): string
    {
        $cellphone = preg_replace('/\D/', '', $cellphone) ?? '';

        if (!preg_match('/^\d{10,11}$/', $cellphone)) {
            throw new DomainException('Celular inválido.');
        }

        return $cellphone;
    }

    private function normalizeCpf(string $cpf): string
    {
        $cpf = preg_replace('/\D/', '', $cpf) ?? '';

        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            throw new DomainException('CPF inválido.');
        }

        for ($position = 9; $position < 11; $position++) {
            $sum = 0;

            for ($index = 0; $index < $position; $index++) {
                $sum += (int) $cpf[$index] * ($position + 1 - $index);
            }

            $digit = (10 * $sum) % 11 % 10;

            if ((int) $cpf[$position] !== $digit) {
                throw new DomainException('CPF inválido.');
            }
        }

        return $cpf;
    }

    private function normalizeUf(string $uf): string
    {
        $uf = strtoupper(trim($uf));

        if (!in_array($uf, [
            'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS',
            'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC',
            'SP', 'SE', 'TO',
        ], true)) {
            throw new DomainException('UF inválida.');
        }

        return $uf;
    }

    private static function hashPassword(string $password): string
    {
        if (strlen($password) < 8) {
            throw new DomainException('A senha deve ter ao menos 8 caracteres.');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        if ($hash === false) {
            throw new RuntimeException('Não foi possível proteger a senha.');
        }

        return $hash;
    }

    private static function validatePasswordHash(string $passwordHash): string
    {
        if (password_get_info($passwordHash)['algo'] === null) {
            throw new DomainException('Hash de senha inválido.');
        }

        return $passwordHash;
    }
}
