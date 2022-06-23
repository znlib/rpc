<?php

namespace ZnLib\Rpc\Domain\Enums\Rbac;

use ZnCore\Base\Enum\Interfaces\GetLabelsInterface;

class RpcSettingsPermissionEnum implements GetLabelsInterface
{

    const VIEW = 'oRpcSettingsView';
    const UPDATE = 'oRpcSettingsUpdate';

    public static function getLabels()
    {
        return [
            self::VIEW => 'Настройки системы. Получить',
            self::UPDATE => 'Настройки системы. Изменить',
        ];
    }
}
