<?php

namespace ZnLib\Rpc\Domain\Exceptions;

use Exception;
use Throwable;
use ZnLib\Rpc\Domain\Enums\RpcErrorCodeEnum;

class ApplicationErrorException extends RpcException
{

    public function __construct($message = 'Application error', $code = RpcErrorCodeEnum::APPLICATION_ERROR, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
