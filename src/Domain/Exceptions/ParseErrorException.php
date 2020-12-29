<?php

namespace ZnLib\Rpc\Domain\Exceptions;

use Exception;
use Throwable;
use ZnLib\Rpc\Domain\Enums\RpcErrorCodeEnum;

class ParseErrorException extends Exception
{

    public function __construct($message = 'Parse error', $code = RpcErrorCodeEnum::PARSE_ERROR, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
