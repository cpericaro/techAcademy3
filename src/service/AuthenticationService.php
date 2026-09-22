<?php

declare(strict_types=1);

namespace Carlos\TechAcademy3\Service;

use Carlos\TechAcademy3\Model\Enum\AccountType;
use Carlos\TechAcademy3\Model\User;
use Carlos\TechAcademy3\Repository\UserRepository;
use DomainException;

final class AuthenticationService
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function login(string $login, string $password): User
    {
        $login = trim($login);
        $user = str_contains($login, '@')
            ? $this->userRepository->findByEmail($login)
            : $this->userRepository->findByUsername($login);

        if ($user === null || !$user->verifyPassword($password)) {
            throw new DomainException('Credenciais inválidas.');
        }

        $id = $user->getId();

        if ($id === null) {
            throw new DomainException('Credenciais inválidas.');
        }

        $this->startSession();
        session_regenerate_id(true);
        $_SESSION['user_id'] = $id;
        $_SESSION['account_type'] = $user->getAccountType()->value;

        return $user;
    }

    public function logout(): void
    {
        $this->startSession();
        $_SESSION = [];
        session_destroy();
    }

    public function getAuthenticatedUser(): ?User
    {
        $this->startSession();
        $id = $_SESSION['user_id'] ?? null;

        if (!is_int($id) && !ctype_digit((string) $id)) {
            return null;
        }

        return $this->userRepository->findById((int) $id);
    }

    public function requireAuthenticatedUser(): User
    {
        $user = $this->getAuthenticatedUser();

        if ($user === null) {
            throw new DomainException('Autenticação necessária.');
        }

        return $user;
    }

    public function requireAccountType(AccountType ...$accountTypes): User
    {
        $user = $this->requireAuthenticatedUser();

        if (!in_array($user->getAccountType(), $accountTypes, true)) {
            throw new DomainException('Acesso não autorizado.');
        }

        return $user;
    }

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
