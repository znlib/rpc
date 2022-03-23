<?php

namespace ZnLib\Rpc\Rpc\Controllers;

use ZnLib\Rpc\Domain\Interfaces\Services\SettingsServiceInterface;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;

class SettingsController
{

    private $service;

    public function __construct(SettingsServiceInterface $systemService)
    {
        $this->service = $systemService;
    }

    public function update(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        $body = $requestEntity->getParams();
        $settingsEntity = $this->service->view();
        EntityHelper::setAttributes($settingsEntity, $body);
        $this->service->update($settingsEntity);
        return new RpcResponseEntity();
    }

    public function view(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        $settingsEntity = $this->service->view();
        return new RpcResponseEntity(EntityHelper::toArray($settingsEntity));
    }
}
