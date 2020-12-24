<?php

namespace ZnLib\Rpc\Rpc\Controllers;

use Exception;
use ZnLib\Fixture\Domain\Services\FixtureService;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;

class FixtureController
{

    private $service;

    public function __construct(FixtureService $service)
    {
        if( ! in_array($_ENV['APP_ENV'], ['dev', 'test'])) {
            throw new Exception('For development or test only!');
        }
        $this->service = $service;
    }

    public function import(RpcRequestEntity $requestEntity)
    {
        if ($_ENV['APP_ENV'] !== 'test') {
            throw new Exception("Launch is possible only in a test environment");
        }
        $fixtures = $requestEntity->getParamItem('fixtures');
        $this->service->importAll($fixtures);
        $resultArray = [
            'count' => count($fixtures),
        ];
        $response = new RpcResponseEntity();
        $response->setResult($resultArray);
        return $response;
    }
}
