<?php

namespace ZnLib\Rpc\Rpc\Base;

use ZnCore\Base\Libs\DotEnv\DotEnv;
use ZnCore\Domain\Base\BaseCrudService;
use ZnCore\Domain\Exceptions\UnprocessibleEntityException;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnCore\Domain\Helpers\ValidationHelper;
use ZnCore\Domain\Libs\Query;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Exceptions\InvalidRequestException;

abstract class BaseCrudRpcController extends BaseRpcController
{

    /**
     * @var $service BaseCrudService
     */
    protected $service;
    protected $pageSizeMax;
    protected $pageSizeDefault;
    protected $filterModel;

    protected function forgeFilterModel(RpcRequestEntity $requestEntity): object
    {
        $filterAttributes = $requestEntity->getParamItem('filter');
        $filterAttributes = $filterAttributes ? $this->removeEmptyParameters($filterAttributes) : [];
        $filterModel = EntityHelper::createEntity($this->filterModel, $filterAttributes);
        try {
            ValidationHelper::validateEntity($filterModel);
        } catch (UnprocessibleEntityException $e) {
            $errorCollection = $e->getErrorCollection();
            $errors = [];
            foreach ($errorCollection as $errorEntity) {
                $errors[] = $errorEntity->getField() . ': ' . $errorEntity->getMessage();
            }
            throw new InvalidRequestException(implode(PHP_EOL, $errors));
        }
        return $filterModel;
    }

    private function removeEmptyParameters(array $filterAttributes): array
    {
        foreach ($filterAttributes as $attribute => $value) {
            if ($value === '') {
                unset($filterAttributes[$attribute]);
            }
        }
        return $filterAttributes;
    }

    protected function forgeQueryByRequest(Query $query, RpcRequestEntity $requestEntity): void
    {
        $this->forgeQueryPagination($query, $requestEntity);
    }

    protected function forgeQueryPagination(Query $query, RpcRequestEntity $requestEntity): void
    {
        // todo: получать data provider, в meta передавать параметры пагинации: totalCount, pageCount, currentPage, perPage
        $this->forgeWith($requestEntity, $query);
        $perPageDefault = $this->pageSizeDefault ?? DotEnv::get('PAGE_SIZE_DEFAULT', 20);
        $perPage = $requestEntity->getParamItem('perPage', $perPageDefault);
        if ($perPage) {
            $query->perPage($perPage);
        }
        $page = $requestEntity->getParamItem('page', 1);
        if ($page) {
            $query->page($page);
        }
    }

    protected function forgeQueryOrder(Query $query, RpcRequestEntity $requestEntity): void
    {
        $order = $requestEntity->getParamItem('order');
        if ($order) {
            $orders = [
                'asc' => SORT_ASC,
                'desc' => SORT_DESC
            ];

            foreach ($order as $key => $value) {
                $order[$key] = $orders[$value];
            }

            $query->orderBy($order);
        }
    }

    protected function forgeQueryFilterModel(Query $query, RpcRequestEntity $requestEntity): void
    {

    }

    public function all(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        // todo: получать data provider, в meta передавать параметры пагинации: totalCount, pageCount, currentPage, perPage
        $query = new Query();
        $this->forgeQueryOrder($query, $requestEntity);
        $this->forgeQueryPagination($query, $requestEntity);

        $dp = $this->service->getDataProvider($query);
        $perPageMax = $this->pageSizeMax ?? DotEnv::get('PAGE_SIZE_MAX', 50);
        $dp->getEntity()->setMaxPageSize($perPageMax);

        if ($this->filterModel) {
            $filterModel = $this->forgeFilterModel($requestEntity);
            $query->setFilterModel($filterModel);
            $dp->setFilterModel($filterModel);
        }
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

    public function persist(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        $params = $requestEntity->getParams();
        $entity = $this->service->createEntity($params);
        $this->service->persist($entity);
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
