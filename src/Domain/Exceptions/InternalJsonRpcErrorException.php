<?php

namespace ZnLib\Rpc\Domain\Exceptions;

use Exception;
use Throwable;
use ZnLib\Rpc\Domain\Enums\RpcErrorCodeEnum;

class InternalJsonRpcErrorException extends ServerErrorException
{

    public function __construct($message = 'Internal JSON-RPC error', $code = RpcErrorCodeEnum::SERVER_ERROR_JSON_RPC_ERROR, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
