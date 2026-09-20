<?php

declare(strict_types=1);

use Carlos\TechAcademy3\Controller\UserController;
use Carlos\TechAcademy3\Repository\UserRepository;
use Carlos\TechAcademy3\Service\UserService;

$root = dirname(__DIR__);

require_once $root . '/src/model/enum/AccountType.php';
require_once $root . '/src/model/User.php';
require_once $root . '/src/repository/UserRepository.php';
require_once $root . '/src/service/UserService.php';
require_once $root . '/src/controller/UserController.php';

ob_start();
require $root . '/src/config/Database.php';
ob_end_clean();

if (!isset($conn) || !$conn instanceof PDO) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => ['message' => 'Erro interno do servidor.']]);
    exit;
}

$userController = new UserController(new UserService(new UserRepository($conn)));
$userController->handle(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    is_string($_GET['action'] ?? null) ? $_GET['action'] : '',
);


//https://www.php.net/manual/en/function.ob-start.php
//https://www.w3schools.com/php/ref_output_ob_start.asp