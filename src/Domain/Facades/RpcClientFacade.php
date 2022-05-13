<?php

namespace ZnLib\Rpc\Domain\Facades;

use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Enums\HttpHeaderEnum;
use ZnLib\Rpc\Domain\Libs\RpcAuthProvider;
use ZnLib\Rpc\Domain\Libs\RpcProvider;

class RpcClientFacade
{

    private $authLogin;
    private $authPassword;
    
    public function __construct(string $authLogin = null, string $authPassword = null)
    {
        $this->authLogin = $authLogin;
        $this->authPassword = $authPassword;
    }

    public function send(RpcRequestEntity $request, string $authLogin = null, string $authPassword = null): RpcResponseEntity
    {
        $authLogin = $authLogin ?: $this->authLogin;
        $authPassword = $authPassword ?: $this->authPassword;
        
        $rpcProvider = self::createRpcProvider($_ENV['RPC_URL']);
        $authProvider = new RpcAuthProvider($rpcProvider);
        $authorizationToken = $authProvider->authBy($authLogin, $authPassword);

        //$request = new RpcRequestEntity();
        $request->addMeta(HttpHeaderEnum::AUTHORIZATION, $authorizationToken);
        //$request->setMethod('requestMessage.all');

        $response = $rpcProvider->sendRequestByEntity($request);
        return $response;
    }

    public static function createRpcProvider(string $baseUrl): RpcProvider
    {
        $rpcProvider = new RpcProvider();
        $rpcProvider->setBaseUrl($baseUrl);
        $rpcProvider->getRpcClient()->setHeaders([
            'env-name' => 'test',
        ]);
        $rpcProvider->setDefaultRpcMethodVersion(1);
        return $rpcProvider;
    }
}
