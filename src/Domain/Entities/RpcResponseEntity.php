<?php

namespace ZnLib\Rpc\Domain\Entities;

class RpcResponseEntity {

    protected $jsonrpc;
    protected $meta = [];
    protected $id = null;

    public function getJsonrpc()
    {
        return $this->jsonrpc;
    }

    public function setJsonrpc($jsonrpc): void
    {
        $this->jsonrpc = $jsonrpc;
    }

    public function getMeta(): array
    {
        return $this->meta;
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

    private $error = [];

    public function getError(): array
    {
        return $this->error;
    }

    public function setError(array $error): void
    {
        $this->error = $error;
    }

    protected $result = [];

    public function getResult()
    {
        return $this->result;
    }

    public function setResult($result): void
    {
        $this->result = $result;
    }


}
