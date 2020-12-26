<?php

namespace ZnLib\Rpc\Domain\Entities;

use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Interfaces\Entity\EntityIdInterface;

class RpcRequestEntity implements EntityIdInterface {

    private $jsonrpc;
    private $method;
    private $params = [];
    private $meta = [];
    private $id = null;

    public function __construct(string $method = '', $params = [], $meta = [], int $id = null)
    {
        $this->method = $method;
        $this->params = $params;
        $this->meta = $meta;
        $this->id = $id;
    }

    public function getJsonrpc()
    {
        return $this->jsonrpc;
    }

    public function setJsonrpc($jsonrpc): void
    {
        $this->jsonrpc = $jsonrpc;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setMethod($method): void
    {
        $this->method = $method;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getParamItem(string $key)
    {
        if (array_key_exists($key,$this->params)) {
            if (!empty($this->params[$key])) {
                return $this->params[$key];
            }
        }
        $message = "Param \"$key\" not found";
        throw new \InvalidArgumentException($message);
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function setParamItem(string $key, $value): void
    {
        $this->params[$key] = $value;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function getMetaItem(string $key)
    {
        return ArrayHelper::getValue($this->meta, $key);
    }

    public function addMeta(string $key, string $value): void
    {
        $this->meta[$key] = $value;
    }

    public function setMeta(array $meta): void
    {
        $this->meta = $meta;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }
}
