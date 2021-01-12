<?php

use ZnLib\Rpc\Domain\Interfaces\Services\ProcedureServiceInterface;
use ZnLib\Rpc\Domain\Services\ProcedureService;
use ZnLib\Rpc\Domain\Interfaces\Repositories\ProcedureConfigRepositoryInterface;
use ZnLib\Rpc\Domain\Repositories\Conf\ProcedureConfigRepository;
use ZnLib\Rpc\Domain\Interfaces\Services\ControllerServiceInterface;
use ZnLib\Rpc\Domain\Services\ControllerService;
use ZnLib\Rpc\Domain\Interfaces\Encoders\RequestEncoderInterface;
use ZnLib\Rpc\Domain\Encoders\RequestEncoder;
use ZnLib\Rpc\Domain\Interfaces\Encoders\ResponseEncoderInterface;
use ZnLib\Rpc\Domain\Encoders\ResponseEncoder;

return [
    'singletons' => [
        ProcedureServiceInterface::class => ProcedureService::class,
        ControllerServiceInterface::class => ControllerService::class,
        ProcedureConfigRepositoryInterface::class => ProcedureConfigRepository::class,
        RequestEncoderInterface::class => RequestEncoder::class,
        ResponseEncoderInterface::class => ResponseEncoder::class,
        \ZnLib\Rpc\Domain\Interfaces\Services\DocsServiceInterface::class => \ZnLib\Rpc\Domain\Services\DocsService::class,
        \ZnLib\Rpc\Domain\Interfaces\Repositories\DocsRepositoryInterface::class => \ZnLib\Rpc\Domain\Repositories\File\DocsRepository::class,
    ],
];