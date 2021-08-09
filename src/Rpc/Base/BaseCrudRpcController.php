<?php

namespace ZnLib\Rpc\Rpc\Base;

use ZnCore\Base\Libs\DotEnv\DotEnv;
use ZnCore\Domain\Base\BaseCrudService;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnCore\Domain\Libs\Query;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;

abstract class BaseCrudRpcController extends BaseRpcController
{

    /**
     * @var $service BaseCrudService
     */
    protected $service;
    protected $pageSizeMax;
    protected $pageSizeDefault;

    public function all(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        // todo: получать data provider, в meta передавать параметры пагинации: totalCount, pageCount, currentPage, perPage
        $query = new Query();
        $perPageMax = $this->pageSizeMax ?? DotEnv::get('PAGE_SIZE_MAX', 50);
        $perPageDefault = $this->pageSizeDefault ?? DotEnv::get('PAGE_SIZE_DEFAULT', 20);
        $perPage = $requestEntity->getParamItem('perPage', $perPageDefault);
        if($perPage) {
            $query->perPage($perPage);
        }
        $page = $requestEntity->getParamItem('page', 1);
        if($page) {
            $query->page($page);
        }
        $dp = $this->service->getDataProvider($query);
        $dp->getEntity()->setMaxPageSize($perPageMax);
        $collection = $dp->getCollection();
        $resultArray = EntityHelper::collectionToArray($collection);
        $response = new RpcResponseEntity();
        $response->setResult($resultArray);
        $response->addMeta('perPage', $dp->getPageSize());
        $response->addMeta('totalCount', $dp->getTotalCount());
        $response->addMeta('page', $dp->getPage());
        return $response;
    }

    public function oneById(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        $id = $requestEntity->getParamItem('id');

        $entity = $this->service->oneById($id);

        $data = EntityHelper::toArray($entity);
        $response = new RpcResponseEntity();
        $response->setResult($data);
        return $response;
    }

    public function add(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        $params = $requestEntity->getParams();

        $entity = $this->service->create($params);

        $data = EntityHelper::toArray($entity);
        $response = new RpcResponseEntity();
        $response->setResult($data);
        return $response;
    }

    public function update(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        $id = $requestEntity->getParamItem('id');
        $data = $requestEntity->getParams();
        
        unset($data['id']);

        $this->service->updateById($id, $data);
        $entity = $this->service->oneById($id);

        $data = EntityHelper::toArray($entity);
        $response = new RpcResponseEntity();
        $response->setResult($data);
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
