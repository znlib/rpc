<?php

namespace ZnLib\Rpc\Domain\Exceptions;

use Exception;
use Throwable;
use ZnLib\Rpc\Domain\Enums\RpcErrorCodeEnum;

class ParamNotFoundException extends Exception
{

    public function __construct($message = 'Parameter not found', $code = RpcErrorCodeEnum::INVALID_PARAMS, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
