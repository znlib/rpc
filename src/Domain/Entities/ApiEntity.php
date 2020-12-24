<?php

namespace ZnLib\Rpc\Domain\Entities;

use Symfony\Component\Validator\Constraints as Assert;
use ZnCore\Domain\Interfaces\Entity\ValidateEntityInterface;

class ApiEntity implements ValidateEntityInterface
{

    private $uri = null;

    private $isCrud = null;

    public function validationRules()
    {
        return [
            'uri' => [
                new Assert\NotBlank,
            ],
            'isCrud' => [
                new Assert\NotBlank,
            ],
        ];
    }

    public function setUri($value) : void
    {
        $this->uri = $value;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function setIsCrud($value) : void
    {
        $this->isCrud = $value;
    }

    public function getIsCrud()
    {
        return $this->isCrud;
    }


}

