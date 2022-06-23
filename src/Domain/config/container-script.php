<?php

use ZnCore\Base\Env\Helpers\EnvHelper;

return [
    'singletons' => [
        'ZnLib\\Rpc\\Domain\\Interfaces\\Repositories\\MethodRepositoryInterface' => !EnvHelper::isDev()
            ? 'ZnLib\Rpc\Domain\Repositories\Eloquent\MethodRepository'
            : 'ZnLib\Rpc\Domain\Repositories\File\MethodRepository',
//            : 'ZnLib\Rpc\Domain\Repositories\ConfigManager\MethodRepository',
    ],
    'entities' => [

    ],
];
