<?php

declare(strict_types=1);

use Carlos\TechAcademy3\Config\Database;
use Carlos\TechAcademy3\Controller\AuthenticationController;
use Carlos\TechAcademy3\Controller\ClassController;
use Carlos\TechAcademy3\Controller\LessonController;
use Carlos\TechAcademy3\Controller\StudentController;
use Carlos\TechAcademy3\Controller\UserController;
use Carlos\TechAcademy3\Repository\ClassRepository;
use Carlos\TechAcademy3\Repository\LessonRepository;
use Carlos\TechAcademy3\Repository\StudentRepository;
use Carlos\TechAcademy3\Repository\UserRepository;
use Carlos\TechAcademy3\Service\AuthenticationService;
use Carlos\TechAcademy3\Service\ClassService;
use Carlos\TechAcademy3\Service\LessonService;
use Carlos\TechAcademy3\Service\StudentService;
use Carlos\TechAcademy3\Service\UserService;

$root = dirname(__DIR__);

require_once $root . '/vendor/autoload.php';

$connection = (new Database())->getConnection();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = is_string($_GET['action'] ?? null) ? $_GET['action'] : '';
$resource = is_string($_GET['resource'] ?? null) ? $_GET['resource'] : 'user';

$userRepository = new UserRepository($connection);
$classRepository = new ClassRepository($connection);
$studentRepository = new StudentRepository($connection);
$lessonRepository = new LessonRepository($connection);
$userService = new UserService($userRepository);
$authenticationService = new AuthenticationService($userRepository);
$studentService = new StudentService($studentRepository, $classRepository, $userRepository);
$classService = new ClassService($classRepository, $studentRepository);
$lessonService = new LessonService($lessonRepository, $classRepository, $studentRepository);

$controller = match ($resource) {
    'auth' => new AuthenticationController($authenticationService),
    'student' => new StudentController($studentService, $lessonService, $authenticationService),
    'class' => new ClassController($classService, $authenticationService),
    'lesson' => new LessonController($lessonService, $authenticationService),
    'user' => new UserController($userService, $authenticationService),
    default => null,
};

if ($controller === null) {
    http_response_code(404);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => ['message' => 'Recurso não encontrado.']], JSON_UNESCAPED_UNICODE);
    exit;
}

$controller->handle($method, $action);
