<?php

declare (strict_types = 1);

namespace Carlos\TechAcademy3\Controller;

use Carlos\TechAcademy3\Model\User;
use Carlos\TechAcademy3\Service\AuthenticationService;
use DomainException;
use Throwable;

final class AuthenticationController
{
    public function __construct(private AuthenticationService $authenticationService)
    {
    }

    public function handle(string $method, string $action): void
    {
        try {
            if ($method === 'POST' && $action === 'login') {
                $user = $this->authenticationService->login(
                    $this->post('login') !== '' ? $this->post('login') : $this->post('identifier'),
                    $this->post('password'),
                );

                $this->respond(200, ['data' => $this->userData($user)]);
                return;
            }

            if ($method === 'POST' && $action === 'logout') {
                $this->authenticationService->logout();

                $this->respond(200, ['data' => ['message' => 'Sessão encerrada com sucesso.']]);
                return;
            }

            if ($method === 'GET' && $action === 'me') {
                $user = $this->authenticationService->requireAuthenticatedUser();

                $this->respond(200, ['data' => $this->userData($user)]);
                return;
            }

            $this->respond(400, ['error' => ['message' => 'Ação ou método HTTP inválido.']]);
        } catch (DomainException $exception) {
            $status = match ($exception->getMessage()) {
                'Acesso não autorizado.' => 403,
                default                  => 401,
            };

            $this->respond($status, ['error' => ['message' => $exception->getMessage()]]);
        } catch (Throwable $exception) {
            error_log($exception->getMessage());

            $this->respond(500, ['error' => ['message' => 'Erro interno do servidor.']]);
        }
    }

    private function post(string $field): string
    {
        return is_string($_POST[$field] ?? null) ? $_POST[$field] : '';
    }

    private function userData(User $user): array
    {
        return [
            'id'          => $user->getId(),
            'username'    => $user->getUsername(),
            'name'        => $user->getName(),
            'email'       => $user->getEmail(),
            'cellphone'   => $user->getCellphone(),
            'cpf'         => $user->getCpf(),
            'uf'          => $user->getUf(),
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
