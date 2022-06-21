<?php

namespace ZnLib\Rpc\Rpc\Controllers;

use Exception;
use ZnCore\Base\Libs\App\Helpers\EnvHelper;
use ZnDatabase\Fixture\Domain\Services\FixtureService;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;

/**
 * Class FixtureController
 * @package ZnLib\Rpc\Rpc\Controllers
 * @todo перенести в пакет с фикстурами
 */
class FixtureController
{

    private $service;

    public function __construct(FixtureService $service)
    {
        if (!EnvHelper::isTest()) {
            throw new Exception('Fixture controller for test only!');
        }
        $this->service = $service;
    }

    public function import(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
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
