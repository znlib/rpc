<?php

namespace ZnLib\Rpc\Domain\Exceptions;

use Exception;
use Throwable;
use ZnLib\Rpc\Domain\Enums\RpcErrorCodeEnum;

class InvalidRequestException extends ServerErrorException
{

    public function __construct($message = 'Invalid request', $code = RpcErrorCodeEnum::SERVER_ERROR_INVALID_REQUEST, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
