<?php

class UserModel
{
    private int $id;
    private string $username;
    private string $name;
    private string $email;
    private string $cellphone;
    private string $cpf;
    private string $uf;
    private int $account_type;
    private string $password;
    private string $passwordHash;


    /**
     * Get the value of password
     */ 
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
    * Set the value of password
     */ 
    //cadastro, nova senha
    public function setPassword(string $password): self
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);

        return $this;
    }

    /**
     * Set the value of passwordHash
    */ 
    public function setPasswordHash(string $passwordHash):  self
    {
        $this->password = $passwordHash;

        return $this;
    }

    public function setCellphone(string $cellphone): self
    {
        $this->cellphone = preg_replace('/\D/', '', $cellphone);
        return $this;
    }

    /**
     * Get the value of account_type
     */ 
    public function getAccount_type(): int
    {
        return $this->account_type;
    }

    /**
     * Set the value of account_type
     *
     * @return  self
     */ 
    public function setAccount_type(int $account_type): self
    {
        $this->account_type = $account_type;

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
    public function getId(): int
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