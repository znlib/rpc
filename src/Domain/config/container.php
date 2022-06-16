<?php

return [
    'singletons' => [
        'ZnLib\\Rpc\\Domain\\Interfaces\\Services\\ProcedureServiceInterface' => 'ZnLib\\Rpc\\Domain\\Services\\ProcedureService',
        'ZnLib\\Rpc\\Domain\\Interfaces\\Encoders\\RequestEncoderInterface' => 'ZnLib\\Rpc\\Domain\\Encoders\\RequestEncoder',
        'ZnLib\\Rpc\\Domain\\Interfaces\\Encoders\\ResponseEncoderInterface' => 'ZnLib\\Rpc\\Domain\\Encoders\\ResponseEncoder',
        'ZnLib\\Rpc\\Domain\\Interfaces\\Services\\MethodServiceInterface' => 'ZnLib\\Rpc\\Domain\\Services\\MethodService',
        'ZnLib\\Rpc\\Domain\\Interfaces\\Services\\DocsServiceInterface' => 'ZnLib\\Rpc\\Domain\\Services\\DocsService',
        'ZnLib\\Rpc\\Domain\\Interfaces\\Services\\VersionHandlerServiceInterface' => 'ZnLib\\Rpc\\Domain\\Services\\VersionHandlerService',
        'ZnLib\\Rpc\\Domain\\Interfaces\\Repositories\\VersionHandlerRepositoryInterface' => 'ZnLib\\Rpc\\Domain\\Repositories\\Eloquent\\VersionHandlerRepository',
//        'ZnLib\\Rpc\\Symfony4\\Web\\Controllers\\DocsController' => 'ZnLib\\Rpc\\Symfony4\\Web\\Controllers\\DocsController',
        'ZnLib\\Rpc\\Domain\\Interfaces\\Repositories\\DocsRepositoryInterface' => 'ZnLib\\Rpc\\Domain\\Repositories\\File\\DocsRepository',
        'ZnLib\\Rpc\\Domain\\Interfaces\\Services\\SettingsServiceInterface' => 'ZnLib\\Rpc\\Domain\\Services\\SettingsService',
    ],
    'entities' => [
        'ZnLib\\Rpc\\Domain\\Entities\\MethodEntity' => 'ZnLib\\Rpc\\Domain\\Interfaces\\Repositories\\MethodRepositoryInterface',
        'ZnLib\\Rpc\\Domain\\Entities\\VersionHandlerEntity' => 'ZnLib\\Rpc\\Domain\\Interfaces\\Repositories\\VersionHandlerRepositoryInterface',
    ],
];