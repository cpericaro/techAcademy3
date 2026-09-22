<?php

declare(strict_types=1);

use Carlos\TechAcademy3\Config\Database;
use Carlos\TechAcademy3\Model\Enum\AccountType;
use Carlos\TechAcademy3\Repository\UserRepository;
use Carlos\TechAcademy3\Service\UserService;

require_once dirname(__DIR__) . '/vendor/autoload.php';

function readValue(string $label): string
{
    echo "{$label}: ";
    return trim((string) fgets(STDIN));
}

try {
    $connection = (new Database())->getConnection();
    $userService = new UserService(new UserRepository($connection));

    echo "Cadastro do administrador\n";

    $user = $userService->createAccount(
        readValue('Nome de usuário'),
        readValue('Nome completo'),
        readValue('E-mail'),
        readValue('Celular'),
        readValue('CPF'),
        readValue('UF'),
        AccountType::ADMIN->value,
        readValue('Senha'),
    );

    echo "Administrador {$user->getUsername()} criado com sucesso.\n";
} catch (Throwable $exception) {
    fwrite(STDERR, "Não foi possível criar o administrador: {$exception->getMessage()}\n");
    exit(1);
}
