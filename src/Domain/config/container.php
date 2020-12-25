<?php

use ZnLib\Rpc\Domain\Interfaces\Services\ProcedureServiceInterface;
use ZnLib\Rpc\Domain\Services\ProcedureService;
use ZnLib\Rpc\Domain\Interfaces\Repositories\ProcedureConfigRepositoryInterface;
use ZnLib\Rpc\Domain\Repositories\Conf\ProcedureConfigRepository;

return [
    'singletons' => [
        ProcedureServiceInterface::class => ProcedureService::class,
        ProcedureConfigRepositoryInterface::class => ProcedureConfigRepository::class,
    ],
];