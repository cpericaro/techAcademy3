<?php

namespace Carlos\TechAcademy3\Service;

use Carlos\TechAcademy3\Model\Enum\AccountType;
use Carlos\TechAcademy3\Model\User;
use Carlos\TechAcademy3\Repository\UserRepository;
use DomainException;

class UserService
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function createAccount(
        string $username,
        string $name,
        string $email,
        string $cellphone,
        string $cpf,
        string $uf,
        int $accountType,
        string $password
    ): User {
        try {
            $type = AccountType::from($accountType);
        } catch (\ValueError) {
            throw new DomainException('Tipo de conta inválido.');
        }

        $user = User::register(
            $username,
            $name,
            $email,
            $cellphone,
            $cpf,
            $uf,
            $type,
            $password,
        );

        if ($this->userRepository->findByEmail($email)) {
            throw new DomainException('E-mail indisponível.');
        }
        if ($this->userRepository->findByUsername($user->getUsername())) {
            throw new DomainException('Nome de usuário indisponível.');
        }
        if ($this->userRepository->findByCellphone($user->getCellphone())) {
            throw new DomainException('Celular indisponível.');
        }
        if ($this->userRepository->findByCpf($user->getCpf())) {
            throw new DomainException('CPF indisponível.');
        }

        $this->userRepository->create($user);

        return $user;
    }
}
