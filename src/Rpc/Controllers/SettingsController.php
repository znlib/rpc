<?php

namespace ZnLib\Rpc\Rpc\Controllers;

use ZnCore\Code\Helpers\PropertyHelper;
use ZnDomain\Entity\Helpers\EntityHelper;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Interfaces\Services\SettingsServiceInterface;

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
        PropertyHelper::setAttributes($settingsEntity, $body);
        $this->service->update($settingsEntity);
        return new RpcResponseEntity();
    }

    public function view(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        $settingsEntity = $this->service->view();
        return new RpcResponseEntity(EntityHelper::toArray($settingsEntity));
    }
}
