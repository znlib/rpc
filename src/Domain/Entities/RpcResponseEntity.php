<?php

namespace ZnLib\Rpc\Domain\Entities;

use ZnCore\Domain\Interfaces\Entity\EntityIdInterface;
use ZnCore\Domain\Interfaces\Entity\ValidateEntityInterface;
use Symfony\Component\Validator\Constraints as Assert;
use ZnLib\Rpc\Domain\Enums\RpcVersionEnum;

class RpcResponseEntity implements EntityIdInterface, ValidateEntityInterface
{

    private $jsonrpc = RpcVersionEnum::V2_0;
    private $result = null;
    private $error = null;
    private $meta = [];
    private $id = null;

    public function __construct($result = [], $error = [], $meta = [], int $id = null)
    {
        $this->result = $result;
        $this->error = $error;
        $this->meta = $meta;
        $this->id = $id;
    }

    public function validationRules()
    {
        return [
            'id' => [
                new Assert\NotBlank()
            ],
            'jsonrpc' => [
                new Assert\NotBlank(),
            ],
        ];
    }

    public function getJsonrpc(): string
    {
        return $this->jsonrpc;
    }

    public function setJsonrpc(string $jsonrpc): void
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

    public function getError()
    {
        return $this->error;
    }

    public function setError($error): void
    {
        $this->error = $error;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setResult($result): void
    {
        $this->result = $result;
    }

    public function isError(): bool
    {
        return !empty($this->error);
    }

    public function isSuccess(): bool
    {
        return !$this->isError();
    }
}
