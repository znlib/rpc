<?php

namespace ZnLib\Rpc\Domain\Exceptions;

use Exception;
use Throwable;
use ZnLib\Rpc\Domain\Enums\RpcErrorCodeEnum;

class TransportErrorException extends Exception
{

    public function __construct($message = 'Transport error', $code = RpcErrorCodeEnum::TRANSPORT_ERROR, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
