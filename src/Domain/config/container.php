<?php

use ZnLib\Rpc\Domain\Interfaces\Services\ProcedureServiceInterface;
use ZnLib\Rpc\Domain\Services\ProcedureService;
use ZnLib\Rpc\Domain\Interfaces\Repositories\ProcedureConfigRepositoryInterface;
use ZnLib\Rpc\Domain\Repositories\Conf\ProcedureConfigRepository;
use ZnLib\Rpc\Domain\Interfaces\Services\ControllerServiceInterface;
use ZnLib\Rpc\Domain\Services\ControllerService;

return [
    'singletons' => [
        ProcedureServiceInterface::class => ProcedureService::class,
        ControllerServiceInterface::class => ControllerService::class,
        ProcedureConfigRepositoryInterface::class => ProcedureConfigRepository::class,
    ],
];