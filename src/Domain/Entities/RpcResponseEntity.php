<?php

namespace ZnLib\Rpc\Domain\Entities;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use ZnCore\Arr\Helpers\ArrayHelper;
use ZnCore\Entity\Interfaces\EntityIdInterface;
use ZnCore\Validation\Interfaces\ValidationByMetadataInterface;
use ZnLib\Rpc\Domain\Enums\RpcVersionEnum;

class RpcResponseEntity implements EntityIdInterface, ValidationByMetadataInterface
{

    private $jsonrpc = RpcVersionEnum::V2_0;
    private $result = null;
    private $error = null;
    private $meta = null;
    private $id = null;

    public function __construct($result = null, $error = null, $meta = null, int $id = null)
    {
        $this->result = $result;
        $this->error = $error;
        $this->meta = $meta;
        $this->id = $id;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('id', new Assert\NotBlank);
        $metadata->addPropertyConstraint('jsonrpc', new Assert\NotBlank);
    }

    public function getJsonrpc(): string
    {
        return $this->jsonrpc;
    }

    public function setJsonrpc(string $jsonrpc): void
    {
        $this->jsonrpc = $jsonrpc;
    }

    public function getMetaItem(string $key, $default = null)
    {
        if (empty($this->meta)) {
            return $default;
        }
        return ArrayHelper::getValue($this->meta, $key, $default);
    }

    public function getMeta(): ?array
    {
        return $this->meta;
    }

    public function setMeta(array $meta): void
    {
        $this->meta = $meta;
    }

    public function addMeta(string $key, $value): void
    {
        if (!is_array($this->meta)) {
            $this->meta = [];
        }
        $this->meta[$key] = $value;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getError(): ?array
    {
        return $this->error;
    }

    public function setError(?array $error): void
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
        return $this->error !== null;
    }

    public function isSuccess(): bool
    {
        return !$this->isError();
    }
}
