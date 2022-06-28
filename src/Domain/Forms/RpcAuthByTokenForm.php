<?php

namespace ZnLib\Rpc\Domain\Forms;

class RpcAuthByTokenForm extends BaseRpcAuthForm
{
    
    private $token;

    public function __construct(string $token = null)
    {
        $this->setToken($token);
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }
}
