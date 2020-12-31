<?php

namespace ZnLib\Rpc\Domain\Enums;

class RpcErrorCodeEnum
{

    /**
     * Parse error
     * Invalid JSON was received by the server.
     * An error occurred on the server while parsing the JSON text.
     */
    const PARSE_ERROR_INVALID_JSON = -32700;

    /**
     * Parse error
     * Unsupported encoding.
     */
    const PARSE_ERROR_UNSUPPORTED_ENCODING = -32701;

    /**
     * Parse error
     * Invalid character for encoding.
     */
    const PARSE_ERROR_INVALID_CHARACTER_FOR_ENCODING = -32702;

    /**
     * Server error
     * Invalid Request
     * The JSON sent is not a valid Request object.
     */
    const SERVER_ERROR_INVALID_REQUEST = -32600;

    /**
     * Server error
     * Method not found
     * The method does not exist / is not available.
     */
    const SERVER_ERROR_METHOD_NOT_FOUND = -32601;

    /**
     * Server error
     * Invalid params
     * Invalid method parameter(s).
     */
    const SERVER_ERROR_INVALID_PARAMS = -32602;

    /**
     * Server error
     * Internal JSON-RPC error.
     */
    const SERVER_ERROR_JSON_RPC_ERROR = -32603;

    /**
     * Application error
     */
    const APPLICATION_ERROR = -32500;

    /**
     * System error
     */
    const SYSTEM_ERROR = -32400;

    /**
     * Transport error
     */
    const TRANSPORT_ERROR = -32300;

    /**
     * Server error
     * Reserved for implementation-defined server-errors.
     * -32000 to -32099
     */
}
