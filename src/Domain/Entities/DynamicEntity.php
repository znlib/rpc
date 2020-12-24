<?php

namespace ZnLib\Rpc\Domain\Entities;

use Exception;
use InvalidArgumentException;
use ZnCore\Domain\Interfaces\Entity\EntityAttributesInterface;
use ZnCore\Domain\Interfaces\Entity\EntityIdInterface;
use ZnCore\Domain\Interfaces\Entity\ValidateEntityInterface;

class DynamicEntity implements ValidateEntityInterface, EntityIdInterface, EntityAttributesInterface
{
    private $_attributes = [];
    private $_validationRules = [];

    public function __construct($entityEntity = null, array $attributes = [])
    {
        if($entityEntity) {
            $this->_attributes = $entityEntity->getAttributeNames();
        } else {
            $this->_attributes = $attributes;
        }

        if($entityEntity) {
            $this->_validationRules = $entityEntity->getRules();
        }
    }

    public function attributes()
    {
        return $this->_attributes;
    }

    public function validationRules()
    {
        return $this->_validationRules;
    }

    public function __get($attribute)
    {
        $this->checkHasAttribute($attribute);
        return $this->{$attribute} ?? null;
    }

    public function __set($attribute, $value)
    {
//        $this->checkHasAttribute($attribute);
        $this->{$attribute} = $value;
    }

    public function __call($name, $arguments)
    {
        $method = substr($name, 0, 3);
        $attributeName = substr($name, 3);
        $attributeName = lcfirst($attributeName);
        if ($method == 'get') {
            return $this->__get($attributeName);
        } elseif ($method == 'set') {
            $this->__set($attributeName, $arguments[0]);
            return $this;
        }
        return null;
    }

    private function checkHasAttribute($attribute)
    {
        $has = in_array($attribute, $this->_attributes);
        if( ! $has) {
            throw new Exception('Not found attribute "' . $attribute . '"!');
        }
    }

    public function setId($value): void
    {
        $this->id = $value;
    }

    public function getId()
    {
        return $this->id;
    }
}
