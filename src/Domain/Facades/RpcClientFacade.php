<?php

namespace ZnLib\Rpc\Domain\Facades;

use ZnCore\Base\Enums\EnvEnum;
use ZnCore\Base\Libs\App\Helpers\EnvHelper;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Enums\HttpHeaderEnum;
use ZnLib\Rpc\Domain\Libs\RpcAuthProvider;
use ZnLib\Rpc\Domain\Libs\RpcProvider;

class RpcClientFacade
{

    private $authLogin;
    private $authPassword;
    private $appEnv;

    public function __construct(string $appEnv = null)
    {
        $this->appEnv = $appEnv ?: EnvHelper::getAppEnv();
    }
    
    public function authBy(string $authLogin = null, string $authPassword = null) {
        $this->authLogin = $authLogin;
        $this->authPassword = $authPassword;
    }

    public function send(RpcRequestEntity $request, string $authLogin = null, string $authPassword = null): RpcResponseEntity
    {
        $authLogin = $authLogin ?: $this->authLogin;
        $authPassword = $authPassword ?: $this->authPassword;
        
        $rpcProvider = self::createRpcProvider($_ENV['RPC_URL']);
        $rpcProvider->authByLogin($authLogin, $authPassword);
        
//        $authProvider = new RpcAuthProvider($rpcProvider);
//        $authorizationToken = $authProvider->authBy($authLogin, $authPassword);

        //$request = new RpcRequestEntity();
//        $request->addMeta(HttpHeaderEnum::AUTHORIZATION, $authorizationToken);
        
        
        //$request->setMethod('requestMessage.all');

        $response = $rpcProvider->sendRequestByEntity($request);
        return $response;
    }

    public function createRpcProvider(string $baseUrl, int $rpcVersion = 1): RpcProvider
    {
        $rpcProvider = new RpcProvider();
        $rpcProvider->setBaseUrl($baseUrl);
        if($this->appEnv == EnvEnum::TEST) {
            $rpcProvider->getRpcClient()->setHeaders([
                'env-name' => 'test',
            ]);
        }
        $rpcProvider->setDefaultRpcMethodVersion($rpcVersion);
        return $rpcProvider;
    }
}
