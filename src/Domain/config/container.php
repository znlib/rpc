<?php

/*use ZnCore\Base\Helpers\EnvHelper;
use ZnLib\Rpc\Domain\Encoders\RequestEncoder;
use ZnLib\Rpc\Domain\Encoders\ResponseEncoder;
use ZnLib\Rpc\Domain\Interfaces\Encoders\RequestEncoderInterface;
use ZnLib\Rpc\Domain\Interfaces\Encoders\ResponseEncoderInterface;
use ZnLib\Rpc\Domain\Interfaces\Repositories\ProcedureConfigRepositoryInterface;
use ZnLib\Rpc\Domain\Interfaces\Services\ControllerServiceInterface;
use ZnLib\Rpc\Domain\Interfaces\Services\ProcedureServiceInterface;
use ZnLib\Rpc\Domain\Repositories\Conf\ProcedureConfigRepository;
use ZnLib\Rpc\Domain\Services\ControllerService;
use ZnLib\Rpc\Domain\Services\ProcedureService;*/

return [
    'singletons' => [
//        ProcedureServiceInterface::class => ProcedureService::class,
//        ControllerServiceInterface::class => ControllerService::class,
//        ProcedureConfigRepositoryInterface::class => ProcedureConfigRepository::class,
//        RequestEncoderInterface::class => RequestEncoder::class,
//        ResponseEncoderInterface::class => ResponseEncoder::class,
//        \ZnLib\Rpc\Domain\Interfaces\Services\DocsServiceInterface::class => \ZnLib\Rpc\Domain\Services\DocsService::class,
//        \ZnLib\Rpc\Domain\Interfaces\Repositories\DocsRepositoryInterface::class => \ZnLib\Rpc\Domain\Repositories\File\DocsRepository::class,

        'ZnLib\\Rpc\\Domain\\Interfaces\\Services\\ProcedureServiceInterface' => 'ZnLib\\Rpc\\Domain\\Services\\ProcedureService',
        'ZnLib\\Rpc\\Domain\\Interfaces\\Repositories\\ProcedureConfigRepositoryInterface' => 'ZnLib\\Rpc\\Domain\\Repositories\\Conf\\ProcedureConfigRepository',
        'ZnLib\\Rpc\\Domain\\Interfaces\\Encoders\\RequestEncoderInterface' => 'ZnLib\\Rpc\\Domain\\Encoders\\RequestEncoder',
        'ZnLib\\Rpc\\Domain\\Interfaces\\Encoders\\ResponseEncoderInterface' => 'ZnLib\\Rpc\\Domain\\Encoders\\ResponseEncoder',
        'ZnLib\\Rpc\\Domain\\Interfaces\\Services\\MethodServiceInterface' => 'ZnLib\\Rpc\\Domain\\Services\\MethodService',
        'ZnLib\\Rpc\\Domain\\Interfaces\\Services\\DocsServiceInterface' => 'ZnLib\\Rpc\\Domain\\Services\\DocsService',
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