<?php

namespace ZnLib\Rpc\Domain\Entities;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use ZnCore\Base\Enums\StatusEnum;
use ZnCore\Base\Libs\Enum\Constraints\Enum;
use ZnCore\Base\Libs\Validation\Interfaces\ValidationByMetadataInterface;
use ZnCore\Base\Libs\Entity\Interfaces\UniqueInterface;
use ZnCore\Contract\Domain\Interfaces\Entities\EntityIdInterface;

class VersionHandlerEntity implements ValidationByMetadataInterface, UniqueInterface, EntityIdInterface
{

    private $id = null;

    private $version = null;

    private $handlerClass = null;

    private $statusId = StatusEnum::ENABLED;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('id', new Assert\NotBlank);
        $metadata->addPropertyConstraint('version', new Assert\NotBlank);
        $metadata->addPropertyConstraint('handlerClass', new Assert\NotBlank);
        $metadata->addPropertyConstraint('statusId', new Assert\NotBlank);
        $metadata->addPropertyConstraint('statusId', new Enum([
            'class' => StatusEnum::class,
        ]));
    }

    public function unique() : array
    {
        return [];
    }

    public function setId($value) : void
    {
        $this->id = $value;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setVersion($value) : void
    {
        $this->version = $value;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setHandlerClass($value) : void
    {
        $this->handlerClass = $value;
    }

    public function getHandlerClass()
    {
        return $this->handlerClass;
    }

    public function setStatusId($value) : void
    {
        $this->statusId = $value;
    }

    public function getStatusId()
    {
        return $this->statusId;
    }


}

