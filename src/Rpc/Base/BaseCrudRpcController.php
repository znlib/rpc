<?php

namespace ZnLib\Rpc\Rpc\Base;

use ZnCore\Domain\Base\BaseCrudService;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;

abstract class BaseCrudRpcController extends BaseRpcController
{

    /**
     * @var $service BaseCrudService
     */
    protected $service;

    public function all(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        // todo: получать data provider, в meta передавать параметры пагинации: totalCount, pageCount, currentPage, perPage
        $collection = $this->service->all();
        $resultArray = EntityHelper::collectionToArray($collection);
        $response = new RpcResponseEntity();
        $response->setResult($resultArray);
        return $response;
    }

    public function oneById(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        $id = $requestEntity->getParamItem('id');

        $partnerEntity = $this->service->oneById($id);

        $partner = EntityHelper::toArray($partnerEntity);
        $response = new RpcResponseEntity();
        $response->setResult($partner);
        return $response;
    }

    public function add(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        $data = $requestEntity->getParams();

        $partnerEntity = $this->service->create($data);

        $partner = EntityHelper::toArray($partnerEntity);
        $response = new RpcResponseEntity();
        $response->setResult($partner);
        return $response;
    }

    public function update(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        $id = $requestEntity->getParamItem('id');
        $data = $requestEntity->getParams();
        
        unset($data['id']);

        $this->service->updateById($id, $data);
        $partnerEntity = $this->service->oneById($id);

        $partner = EntityHelper::toArray($partnerEntity);
        $response = new RpcResponseEntity();
        $response->setResult($partner);
        return $response;
    }

    public function delete(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        $id = $requestEntity->getParamItem('id');

        $this->service->deleteById($id);
        $result = "";
        $response = new RpcResponseEntity();
        $response->setResult($result);
        return $response;
    }

    public function count(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        $result = $this->service->count();
        $response = new RpcResponseEntity();
        $response->setResult($result);
        return $response;
    }
}
