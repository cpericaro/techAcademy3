<?php

declare(strict_types=1);

namespace Carlos\TechAcademy3\Controller;

use Carlos\TechAcademy3\Model\User;
use Carlos\TechAcademy3\Service\UserService;
use DomainException;
use Throwable;

final class UserController
{
    public function __construct(private UserService $userService)
    {
    }

    public function handle(string $method, string $action): void
    {
        try {
            if ($method === 'POST' && $action === 'create') {
                $user = $this->userService->createAccount(
                    $this->post('username'),
                    $this->post('name'),
                    $this->post('email'),
                    $this->post('cellphone'),
                    $this->post('cpf'),
                    $this->post('uf'),
                    (int) $this->post('accountType'),
                    $this->post('password'),
                );

                $this->respond(200, ['data' => $this->userData($user)]);
                return;
            }

            if ($method === 'GET' && $action === 'show') {
                $user = $this->userService->findAccount($this->query('username'));

                $this->respond(200, ['data' => $this->userData($user)]);
                return;
            }

            $this->respond(400, ['error' => ['message' => 'Ação ou método HTTP inválido.']]);
        } catch (DomainException $exception) {
            $status = $action === 'show' && $exception->getMessage() === 'Conta não encontrada.'
                ? 404
                : 400;

            $this->respond($status, ['error' => ['message' => $exception->getMessage()]]);
        } catch (Throwable) {
            $this->respond(500, ['error' => ['message' => 'Erro interno do servidor.']]);
        }
    }

    private function post(string $field): string
    {
        return is_string($_POST[$field] ?? null) ? $_POST[$field] : '';
    }

    private function query(string $field): string
    {
        return is_string($_GET[$field] ?? null) ? $_GET[$field] : '';
    }

    private function userData(User $user): array
    {
        return [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'cellphone' => $user->getCellphone(),
            'uf' => $user->getUf(),
            'accountType' => $user->getAccountType()->value,
        ];
    }

    private function respond(int $status, array $body): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
}

//https://www.w3schools.com/php/php_forms.asp
//https://www.w3schools.com/php/php_json.asp