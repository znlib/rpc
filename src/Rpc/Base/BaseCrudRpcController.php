<?php

namespace ZnLib\Rpc\Rpc\Base;

use ZnCore\Base\Libs\DotEnv\DotEnv;
use ZnCore\Domain\Base\BaseCrudService;
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

    public function allowRelations(): array
    {
        return [

        ];
    }

    private function forgeWith(RpcRequestEntity $requestEntity, Query $query)
    {
        $with = $requestEntity->getParamItem('with');
        if ($with) {
            foreach ($with as $relationName) {
                if (in_array($relationName, $this->allowRelations())) {
                    $query->with($relationName);
                }
            }
        }
    }

    public function all(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        // todo: получать data provider, в meta передавать параметры пагинации: totalCount, pageCount, currentPage, perPage
        $query = new Query();
        $this->forgeWith($requestEntity, $query);
        $perPageMax = $this->pageSizeMax ?? DotEnv::get('PAGE_SIZE_MAX', 50);
        $perPageDefault = $this->pageSizeDefault ?? DotEnv::get('PAGE_SIZE_DEFAULT', 20);
        $perPage = $requestEntity->getParamItem('perPage', $perPageDefault);
        if ($perPage) {
            $query->perPage($perPage);
        }
        $page = $requestEntity->getParamItem('page', 1);
        if ($page) {
            $query->page($page);
        }

        $dp = $this->service->getDataProvider($query);
        $dp->getEntity()->setMaxPageSize($perPageMax);

        return $this->serializeResult($dp);
    }

    public function oneById(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        $query = new Query();
        $this->forgeWith($requestEntity, $query);
        $id = $requestEntity->getParamItem('id');
        $entity = $this->service->oneById($id, $query);

        return $this->serializeResult($entity);
    }

    public function add(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        $params = $requestEntity->getParams();
        $entity = $this->service->create($params);

        return $this->serializeResult($entity);
    }

    public function update(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        $id = $requestEntity->getParamItem('id');
        $data = $requestEntity->getParams();

        unset($data['id']);

        $this->service->updateById($id, $data);
        $entity = $this->service->oneById($id);

        return $this->serializeResult($entity);
    }

    public function delete(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        $id = $requestEntity->getParamItem('id');
        $this->service->deleteById($id);
        return new RpcResponseEntity();
    }

    public function count(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        $result = $this->service->count();
        return $this->serializeResult($result);
    }
}
