<?php

namespace ZnLib\Rpc\Domain\Forms;

class RpcAuthByLoginForm extends BaseRpcAuthForm
{
    
    private $login;
    private $password;

    public function __construct(string $login, ?string $password = null)
    {
        $this->setLogin($login);
        $this->setPassword($password);
    }

    public function getLogin(): ?string 
    {
        return $this->login;
    }

    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }
}
