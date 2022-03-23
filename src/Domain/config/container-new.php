<?php

use ZnCore\Base\Helpers\EnvHelper;

return [
    'definitions' => [],
    'singletons' => [
        'ZnLib\\Rpc\\Domain\\Interfaces\\Services\\MethodServiceInterface' => 'ZnLib\\Rpc\\Domain\\Services\\MethodService',

        'ZnLib\\Rpc\\Domain\\Interfaces\\Repositories\\MethodRepositoryInterface' => !EnvHelper::isDev()
            ? 'ZnLib\Rpc\Domain\Repositories\Eloquent\MethodRepository'
            : 'ZnLib\Rpc\Domain\Repositories\File\MethodRepository',
//            : 'ZnLib\Rpc\Domain\Repositories\ConfigManager\MethodRepository',
        
        'ZnLib\\Rpc\\Domain\\Interfaces\\Services\\VersionHandlerServiceInterface' => 'ZnLib\\Rpc\\Domain\\Services\\VersionHandlerService',
        'ZnLib\\Rpc\\Domain\\Interfaces\\Repositories\\VersionHandlerRepositoryInterface' => 'ZnLib\\Rpc\\Domain\\Repositories\\Eloquent\\VersionHandlerRepository',
        'ZnLib\\Rpc\\Symfony4\\Web\\Controllers\\RpcController' => 'ZnLib\\Rpc\\Symfony4\\Web\\Controllers\\RpcController',
        'ZnLib\\Rpc\\Symfony4\\Web\\Controllers\\DocsController' => 'ZnLib\\Rpc\\Symfony4\\Web\\Controllers\\DocsController',
        'ZnLib\\Rpc\\Symfony4\\Web\\Controllers\\DefaultController' => 'ZnLib\\Rpc\\Symfony4\\Web\\Controllers\\DefaultController',
        'ZnLib\\Rpc\\Domain\\Interfaces\\Repositories\\DocsRepositoryInterface' => 'ZnLib\\Rpc\\Domain\\Repositories\\File\\DocsRepository',
        'ZnLib\\Rpc\\Domain\\Interfaces\\Services\\SettingsServiceInterface' => 'ZnLib\\Rpc\\Domain\\Services\\SettingsService',
    ],
    'entities' => [
        'ZnLib\\Rpc\\Domain\\Entities\\MethodEntity' => 'ZnLib\\Rpc\\Domain\\Interfaces\\Repositories\\MethodRepositoryInterface',
        'ZnLib\\Rpc\\Domain\\Entities\\VersionHandlerEntity' => 'ZnLib\\Rpc\\Domain\\Interfaces\\Repositories\\VersionHandlerRepositoryInterface',
    ],
];