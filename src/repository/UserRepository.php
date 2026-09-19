<?php

declare(strict_types=1);

namespace Carlos\TechAcademy3\Repository;

use Carlos\TechAcademy3\Model\Enum\AccountType;
use Carlos\TechAcademy3\Model\User;
use PDO;

final class UserRepository
{
    public function __construct(private PDO $connection)
    {
    }

    public function create(User $user): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO `USER` (`USERNAME`, `NAME`, `EMAIL`, `PASSWORD_HASH`, `CELLPHONE`, `CPF`, `UF`, `TYPE`)
             VALUES (:username, :name, :email, :password_hash, :cellphone, :cpf, :uf, :type)'
        );
        $statement->execute([
            'username' => $user->getUsername(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password_hash' => $user->getPasswordHash(),
            'cellphone' => $user->getCellphone(),
            'cpf' => $user->getCpf(),
            'uf' => $user->getUf(),
            'type' => $user->getAccountType()->value,
        ]);

        $user->assignId((int) $this->connection->lastInsertId());
    }

    public function findByEmail(string $email): ?User
    {
        return $this->findOneBy('EMAIL', strtolower(trim($email)));
    }

    public function findByUsername(string $username): ?User
    {
        return $this->findOneBy('USERNAME', trim($username));
    }

    public function findByCellphone(string $cellphone): ?User
    {
        return $this->findOneBy('CELLPHONE', preg_replace('/\D/', '', $cellphone) ?? '');
    }

    public function findByCpf(string $cpf): ?User
    {
        return $this->findOneBy('CPF', preg_replace('/\D/', '', $cpf) ?? '');
    }

    private function findOneBy(string $column, string $value): ?User
    {
        $statement = $this->connection->prepare(
            "SELECT `ID`, `USERNAME`, `NAME`, `EMAIL`, `PASSWORD_HASH`, `CELLPHONE`, `CPF`, `UF`, `TYPE`
             FROM `USER` WHERE `{$column}` = :value LIMIT 1"
        );
        $statement->execute(['value' => $value]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return User::fromDatabase(
            (int) $row['ID'],
            $row['USERNAME'],
            $row['NAME'],
            $row['EMAIL'],
            $row['CELLPHONE'],
            $row['CPF'],
            $row['UF'],
            AccountType::from((int) $row['TYPE']),
            $row['PASSWORD_HASH'],
        );
    }
}

