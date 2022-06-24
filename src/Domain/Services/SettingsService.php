<?php

namespace ZnLib\Rpc\Domain\Services;

use ZnCore\Base\Validation\Helpers\ValidationHelper;
use ZnCore\Domain\Entity\Helpers\EntityHelper;
use ZnLib\Rpc\Domain\Entities\SettingsEntity;
use ZnLib\Rpc\Domain\Interfaces\Services\SettingsServiceInterface;
use ZnSandbox\Sandbox\Settings\Domain\Interfaces\Services\SystemServiceInterface;

class SettingsService implements SettingsServiceInterface
{

    private $systemService;

    public function __construct(SystemServiceInterface $systemService)
    {
        $this->systemService = $systemService;
    }

    public function getEntityClass(): string
    {
        return SettingsEntity::class;
    }

    public function update(SettingsEntity $settingsEntity)
    {
        ValidationHelper::validateEntity($settingsEntity);
        $settingsData = EntityHelper::toArray($settingsEntity);
        $this->systemService->update('rpc', $settingsData);
    }

    public function view(): SettingsEntity
    {
        $data = $this->systemService->view('rpc');
        $settingsEntity = new SettingsEntity();
        EntityHelper::setAttributes($settingsEntity, $data);
        return $settingsEntity;
    }
}
