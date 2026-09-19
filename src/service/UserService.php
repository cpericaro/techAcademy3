<?php

declare(strict_types=1);

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

    public function findAccount(string $username): User
    {
        $user = $this->userRepository->findByUsername($username);

        if ($user === null) {
            throw new DomainException('Conta não encontrada.');
        }

        return $user;
    }

    public function editAccount(
        string $username,
        string $name,
        string $email,
        string $cellphone,
        string $cpf,
        string $uf,
    ): User {
        $user = $this->findAccount($username);
        $previousEmail = $user->getEmail();
        $previousCellphone = $user->getCellphone();
        $previousCpf = $user->getCpf();

        $user->updateProfile($name, $email, $cellphone, $cpf, $uf);

        if ($user->getEmail() !== $previousEmail && $this->userRepository->findByEmail($user->getEmail())) {
            throw new DomainException('E-mail indisponível.');
        }
        if ($user->getCellphone() !== $previousCellphone && $this->userRepository->findByCellphone($user->getCellphone())) {
            throw new DomainException('Celular indisponível.');
        }
        if ($user->getCpf() !== $previousCpf && $this->userRepository->findByCpf($user->getCpf())) {
            throw new DomainException('CPF indisponível.');
        }

        $this->userRepository->updateProfile($user);

        return $user;
    }

    public function changeAccountPassword(
        string $username,
        string $currentPassword,
        string $newPassword,
    ): void {
        $user = $this->findAccount($username);

        if (!$user->verifyPassword($currentPassword)) {
            throw new DomainException('Senha atual incorreta.');
        }

        $user->changePassword($newPassword);
        $this->userRepository->updatePassword($user);
    }

    public function deleteAccount(string $username): void
    {
        $user = $this->findAccount($username);
        $this->userRepository->delete($user);
    }
}
