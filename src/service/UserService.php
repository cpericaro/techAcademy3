<?php

namespace Carlos\TechAcademy3\Service;

use Carlos\TechAcademy3\Model\userModel;
use Carlos\TechAcademy3\Repository\UserRepository;

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
    ): userModel {

        //regras de negocio
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('E-mail inválido');
        }
        if ($this->userRepository->findByEmail($email)) {
            throw new \Exception('E-mail indisponível');
        }
        if ($this->userRepository->findByUsername($username)) {
            throw new \Exception('Nome de usuario indisponivel');
        }
        if ($this->userRepository->findByCellphone($cellphone)) {
            throw new \Exception('Celular indisponivel');
        }

        //declaracao do usuario
        $user = new userModel();
        $user->setUsername($username)
             ->setName($name)
             ->setEmail($email)
             ->setCellphone($cellphone)
             ->setCpf($cpf)
             ->setUf($uf)
             ->setAccount_type($accountType)
             ->setPassword($password); // hashed pelo setter

        $this->userRepository->create($user);

        return $user;
    }
}