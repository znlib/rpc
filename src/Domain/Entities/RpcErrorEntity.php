<?php

namespace ZnLib\Rpc\Domain\Entities;

class RpcErrorEntity
{

    private $code = null;
    private $message = null;
    private $data = null;

    public function __construct(int $code, string $message = null, $data = null)
    {
        $this->setCode($code);
        $this->setMessage($message);
        $this->setData($data);
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(?int $code): void
    {
        $this->code = $code;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data): void
    {
        $this->data = $data;
    }
}
