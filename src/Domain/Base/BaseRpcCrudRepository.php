<?php

namespace ZnLib\Rpc\Domain\Base;

use Illuminate\Support\Enumerable;
use ZnCore\Domain\Entity\Interfaces\EntityIdInterface;
use ZnCore\Domain\Entity\Interfaces\UniqueInterface;
use ZnCore\Domain\Query\Entities\Query;
use ZnCore\Domain\QueryFilter\Interfaces\ForgeQueryByFilterInterface;
use ZnCore\Domain\Repository\Interfaces\CrudRepositoryInterface;
use ZnCore\Domain\Repository\Interfaces\FindOneUniqueInterface;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Enums\HttpHeaderEnum;
use ZnLib\Rpc\Domain\Enums\RpcVersionEnum;
use ZnLib\Rpc\Domain\Forms\BaseRpcAuthForm;
use ZnLib\Rpc\Domain\Forms\RpcAuthGuestForm;

abstract class BaseRpcCrudRepository extends BaseRpcRepository implements CrudRepositoryInterface, ForgeQueryByFilterInterface, FindOneUniqueInterface
{

    abstract public function methodPrefix(): string;

    public function count(Query $query = null): int
    {

    }

    public function all(Query $query = null): Enumerable
    {
        $requestEntity = $this->createRequest('all');
        $responseEntity = $this->sendRequestByEntity($requestEntity);
        $collection = $this->getEntityManager()->createEntityCollection($this->getEntityClass(), $responseEntity->getResult());
        return $collection;
    }

    protected function createRequest(string $methodName = null): RpcRequestEntity {
        $requestEntity = new RpcRequestEntity();
        $requestEntity->setJsonrpc(RpcVersionEnum::V2_0);
        $requestEntity->setMetaItem(HttpHeaderEnum::VERSION, 1);
        if($methodName) {
            $requestEntity->setMethod($this->methodPrefix() . '.' . $methodName);
        }
        return $requestEntity;
    }

    public function authBy(): BaseRpcAuthForm
    {
        return new RpcAuthGuestForm();
    }

    protected function sendRequestByEntity(RpcRequestEntity $requestEntity, BaseRpcAuthForm $authForm = null): RpcResponseEntity
    {
        $provider = $this->getRpcProvider();
        $authForm = $authForm ?: $this->authBy();
        if (!$authForm instanceof RpcAuthGuestForm) {
            $provider->authByForm($authForm);
        }
        $responseEntity = $provider->sendRequestByEntity($requestEntity);
        return $responseEntity;
    }

    public function oneById($id, Query $query = null): EntityIdInterface
    {
        // TODO: Implement oneById() method.
    }

    public function oneByUnique(UniqueInterface $entity): EntityIdInterface
    {
        // TODO: Implement oneByUnique() method.
    }

    public function forgeQueryByFilter(object $filterModel, Query $query)
    {
        // TODO: Implement forgeQueryByFilter() method.
    }

    public function create(EntityIdInterface $entity)
    {
        // TODO: Implement create() method.
    }

    public function update(EntityIdInterface $entity)
    {
        // TODO: Implement update() method.
    }

    public function deleteById($id)
    {
        // TODO: Implement deleteById() method.
    }

    public function deleteByCondition(array $condition)
    {
        // TODO: Implement deleteByCondition() method.
    }
}
