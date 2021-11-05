<?php

namespace ZnLib\Rpc\Domain\Interfaces\Services;

use ZnSandbox\Sandbox\Rpc\Domain\Entities\SettingsEntity;

interface SettingsServiceInterface
{

    public function update(SettingsEntity $settingsEntity);
    public function view(): SettingsEntity;
}

