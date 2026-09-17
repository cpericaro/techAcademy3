<?php

declare(strict_types=1);
namespace Carlos\TechAcademy3\Model;

use Carlos\TechAcademy3\Model\Enum\AccountType;
use DomainException;

class User
{
    private ?int $id = null;
    private string $username;
    private string $name;
    private string $email;
    private string $cellphone;
    private string $cpf;
    private string $uf;
    private AccountType $accountType;
    private string $passwordHash;

    //usar __constructor e nao getters e setters, fere SOLID
    public function __construct(
        string $username, 
        string $name, 
        string $email, 
        string $cellphone,
        string $cpf,
        string $uf,
        AccountType $accountType,
    ){
        $this->username = $username;
        $this->name = $name;
        $this->email = $email;
        $this->cellphone = $cellphone;
        $this->cpf = $cpf;
        $this->uf = $uf;
        $this->accountType = $accountType;
        //chjamar as validações aq
        
    }

    $hash = password_hash($senha, PASSWORD_DEFAULT, $options);
    $user->salvarNovaSenha($hash);


    //inserir validação de email, cpf, telefone e outros aqui na classe model


    //metodo baseado no stackoverflow ja exisntente
    private function validateCpf(string $cpf): void{
    
    $cpf = preg_replace('/\D/', '', $cpf);

    // Verifica se tem 11 dígitos
    if (strlen($cpf) != 11) {
        throw new DomainException("Comprimento do CPF Inválido", 1);
    }
    //DomainException utilizado pois viola regra de negocio, e nao é um erro generico

    // Rejeita CPFs com sequências repetidas conhecidas
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        throw new DomainException("Formato do CPF Inválido", 1);
    }

    // Calcula o primeiro dígito verificador
    for ($t = 9; $t < 11; $t++) {
        $d = 0;
        for ($c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            throw new DomainException("CPF Inválido", 1);
        }
    }
    
    }


    //utilizei a extensao PHP Getters & Setters para fazer os mesmos de forma automatica


    public function setCellphone(string $cellphone): self
    {
        $this->cellphone = preg_replace('/\D/', '', $cellphone);
        return $this;
    }

    /**
     * Get the value of account_type
     */ 
    public function getAccountType(): AccountType
    {
        return $this->accountType;
    }

    /**
     * Set the value of account_type
     *
     * @return  self
     */ 
    public function setAccountType(int $accountType): self
    {
        $this->accountType = $accountType;

        return $this;
    }

    /**
     * Get the value of cpf
     */ 
    public function getCpf(): string
    {
        return $this->cpf;
    }

    /**
     * Set the value of cpf
     *
     * @return  self
     */ 
    public function setCpf(string $cpf): self
    {
        $this->cpf = $cpf;

        return $this;
    }

    /**
     * Get the value of cellphone
     */ 
    public function getCellphone(): string
    {
        return $this->cellphone;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */ 
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of name
     */ 
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of username
     */ 
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Set the value of username
     *
     * @return  self
     */ 
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the value of uf
     */ 
    public function getUf(): string
    {
        return $this->uf;
    }

    /**
     * Set the value of uf
     *
     * @return  self
     */ 
    public function setUf(string $uf): self
    {
        $this->uf = $uf;

        return $this;
    }

    /**
     * Get the value of id
     */ 
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }


}

?>