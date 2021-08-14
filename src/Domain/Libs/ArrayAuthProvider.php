<?php

namespace ZnLib\Rpc\Domain\Libs;

use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;

class ArrayAuthProvider
{

    protected $rpcProvider;
    protected $tokens = [];

    public function __construct(RpcProvider $rpcProvider)
    {
        $this->rpcProvider = $rpcProvider;
    }

    public function setTokens(array $tokens): void
    {
        $this->tokens = $tokens;
    }

    public function authRequest(string $login, string $password): RpcResponseEntity
    {
        $request = new RpcRequestEntity();
        $request->setMethod('authentication.getTokenByPassword');
        $request->setParams([
            'login' => $login,
            'password' => $password,
        ]);
        $response = $this->rpcProvider->sendRequestByEntity($request);

        /*$response = $this->rpcProvider->sendRequest('authentication.getTokenByPassword', [
            'login' => $login,
            'password' => $password,
        ]);*/
        return $response;
    }

    public function authBy(string $login, string $password): string
    {
        return $this->tokens[$login];
        /*$response = $this->authRequest($login, $password);
        return $response->getResult()['token'];*/
    }
}
