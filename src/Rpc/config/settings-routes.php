<?php

use ZnLib\Rpc\Domain\Enums\Rbac\RpcSettingsPermissionEnum;
use ZnLib\Rpc\Rpc\Controllers\SettingsController;

return [
    [
        'method_name' => 'rpcSettings.update',
        'version' => '1',
        'is_verify_eds' => false,
        'is_verify_auth' => true,
        'permission_name' => RpcSettingsPermissionEnum::UPDATE,
        'handler_class' => SettingsController::class,
        'handler_method' => 'update',
        'status_id' => 100,
    ],
    [
        'method_name' => 'rpcSettings.view',
        'version' => '1',
        'is_verify_eds' => false,
        'is_verify_auth' => true,
        'permission_name' => RpcSettingsPermissionEnum::VIEW,
        'handler_class' => SettingsController::class,
        'handler_method' => 'view',
        'status_id' => 100,
    ],
];
