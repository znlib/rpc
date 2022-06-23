<?php

namespace ZnLib\Rpc\Domain\Helpers;

use ZnCore\Base\App\Enums\EnvEnum;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Facades\RpcClientFacade;

class RpcTestHelper
{

    public static function sendRpcRequest(RpcRequestEntity $requestEntity, string $login = null): RpcResponseEntity
    {
        $rpcClientFacade = new RpcClientFacade(EnvEnum::TEST);
//        $rpcClientFacade->authBy($login, 'Wwwqqq111');
        $response = $rpcClientFacade->send($requestEntity, $login, 'Wwwqqq111');
        return $response;
    }
}
