<?php

namespace ZnLib\Rpc\Domain\Entities;

class HandlerEntity {

    private $class;
    private $method;
    private $parameters = [];
    private $isCheckIp = true;
    private $attributes = [];
    private $access = [];

    public function getClass(): string
    {
        return $this->class;
    }

    public function setClass(string $class): void
    {
        $this->class = $class;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function isCheckIp(): bool
    {
        return $this->isCheckIp;
    }

    public function setIsCheckIp(bool $isCheckIp): void
    {
        $this->isCheckIp = $isCheckIp;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function getAccess(): array
    {
        return $this->access;
    }

    public function setAccess(array $access): void
    {
        $this->access = $access;
    }
}
