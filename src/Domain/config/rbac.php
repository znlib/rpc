<?php

use ZnUser\Rbac\Domain\Enums\Rbac\SystemRoleEnum;
use ZnLib\Rpc\Domain\Enums\Rbac\RpcDocPermissionEnum;
use ZnLib\Rpc\Domain\Enums\Rbac\RpcSettingsPermissionEnum;
use ZnLib\Rpc\Domain\Enums\Rbac\FixturePermissionEnum;

return [
    'roleEnums' => [

    ],
    'permissionEnums' => [
        RpcDocPermissionEnum::class,
        RpcSettingsPermissionEnum::class,
        FixturePermissionEnum::class,
    ],
    'inheritance' => [
        SystemRoleEnum::GUEST => [
            FixturePermissionEnum::FIXTURE_IMPORT,
        ],
        SystemRoleEnum::USER => [

        ],
        SystemRoleEnum::ADMINISTRATOR => [
            RpcDocPermissionEnum::ALL,
            RpcDocPermissionEnum::ONE,
            RpcDocPermissionEnum::DOWNLOAD,

            RpcSettingsPermissionEnum::UPDATE,
            RpcSettingsPermissionEnum::VIEW,
        ],
    ],
];
