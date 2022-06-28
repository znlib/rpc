<?php

namespace ZnLib\Rpc\Domain\Libs;

use ZnCore\Base\Validation\Exceptions\UnprocessibleEntityException;
use ZnCore\Base\Validation\Helpers\ErrorCollectionHelper;
use ZnCore\Domain\Entity\Exceptions\NotFoundException;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Enums\RpcErrorCodeEnum;

class RpcErrorHandler
{

    public function handle(RpcResponseEntity $rpcResponseEntity)
    {
        $errorCode = $rpcResponseEntity->getError()['code'];
        $message = $rpcResponseEntity->getError()['message'];
        if ($errorCode == RpcErrorCodeEnum::SERVER_ERROR_INVALID_PARAMS) {
            $errors = $rpcResponseEntity->getError()['data'];
            $errorCollection = ErrorCollectionHelper::itemArrayToCollection($errors);
            throw new UnprocessibleEntityException($errorCollection);
        }

        if ($errorCode == 404) {
            throw new NotFoundException($message);
        }

        throw new \Exception($message);
    }
}
