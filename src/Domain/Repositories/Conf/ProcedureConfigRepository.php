<?php

namespace ZnLib\Rpc\Domain\Repositories\Conf;

use ZnCore\Domain\Entity\Exceptions\NotFoundException;
use ZnCore\Base\Develop\Helpers\DeprecateHelper;
use ZnCore\Base\Arr\Helpers\ArrayHelper;
use ZnCore\Domain\Entity\Helpers\EntityHelper;
use ZnLib\Rpc\Domain\Entities\HandlerEntity;
use ZnLib\Rpc\Domain\Exceptions\MethodNotFoundException;
use ZnLib\Rpc\Domain\Interfaces\Repositories\ProcedureConfigRepositoryInterface;

DeprecateHelper::hardThrow();

class ProcedureConfigRepository implements ProcedureConfigRepositoryInterface
{

    private $busConfig = [];

    public function __construct(array $busConfig)
    {
        $this->busConfig = $busConfig;
    }

    public function oneByMethodName(string $method): HandlerEntity
    {
        try {
            $handlerEntity = $this->getHandlerByName($method);
        } catch (NotFoundException $exception) {
            $args = explode(".", $method);
            if(count($args) < 2) {
                throw new NotFoundException('Not found handler');
            }
            $handlerEntity = $this->getHandlerByName($args[0]);
            $handlerEntity->setMethod($args[1]);
        }
        return $handlerEntity;
    }

    private function getHandlerByName(string $name): HandlerEntity
    {
        $handler = ArrayHelper::getValue($this->busConfig, $name);
        if (!$handler) {
            throw new NotFoundException('Not found handler');
        }
        $handlerEntity = EntityHelper::createEntity(HandlerEntity::class, $handler);
        return $handlerEntity;
    }
}
