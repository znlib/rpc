<?php

namespace ZnLib\Rpc\Domain\Repositories\Conf;

use ZnLib\Rpc\Domain\Entities\HandlerEntity;
use ZnLib\Rpc\Domain\Exceptions\MethodNotFoundException;
use ZnCore\Base\Exceptions\NotFoundException;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnLib\Rpc\Domain\Interfaces\Repositories\ProcedureConfigRepositoryInterface;

class ProcedureConfigRepository implements ProcedureConfigRepositoryInterface
{

    private $busConfig = [];

    public function __construct(array $busConfig)
    {
        $this->busConfig = $busConfig;
    }

    public function getHandlerByName(string $name): HandlerEntity
    {
        $handler = ArrayHelper::getValue($this->busConfig, $name);
        if (!$handler) {
            throw new MethodNotFoundException('Not found handler');
//            $handler = ArrayHelper::getValue($procedureMap, 'default');
        }
        $handlerEntity = EntityHelper::createEntity(HandlerEntity::class, $handler);
        return $handlerEntity;
    }

    public function getServiceByName(string $name): array
    {
        $handler = ArrayHelper::getValue($this->busConfig, $name);
        if (!$handler) {
            throw new MethodNotFoundException('Not found handler');
//            $handler = ArrayHelper::getValue($procedureMap, 'default');
        }
        return $handler;
    }
}
