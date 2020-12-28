<?php

namespace ZnLib\Rpc\Domain\Repositories\Conf;

use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnLib\Rpc\Domain\Entities\HandlerEntity;
use ZnLib\Rpc\Domain\Exceptions\MethodNotFoundException;
use ZnLib\Rpc\Domain\Interfaces\Repositories\ProcedureConfigRepositoryInterface;

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
        } catch (MethodNotFoundException $exception) {
            $handlerParams = explode(".", $method);
            $controller = $handlerParams[0];
            $action = isset($handlerParams[1]) ? $handlerParams[1] : "";
            $handlerEntity = $this->getHandlerByName($controller);
            $handlerEntity->setMethod($action);
        }
        return $handlerEntity;
    }

    private function getHandlerByName(string $name): HandlerEntity
    {
        $handler = ArrayHelper::getValue($this->busConfig, $name);
        if (!$handler) {
            throw new MethodNotFoundException('Not found handler');
        }
        $handlerEntity = EntityHelper::createEntity(HandlerEntity::class, $handler);
        return $handlerEntity;
    }
}
