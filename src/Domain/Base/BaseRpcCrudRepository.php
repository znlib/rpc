<?php

namespace ZnLib\Rpc\Domain\Base;

use Illuminate\Support\Enumerable;
use ZnCore\Domain\DataProvider\Interfaces\DataProviderInterface;
use ZnCore\Domain\DataProvider\Libs\DataProvider;
use ZnCore\Domain\Entity\Interfaces\EntityIdInterface;
use ZnCore\Domain\Entity\Interfaces\UniqueInterface;
use ZnCore\Domain\Query\Entities\Query;
use ZnCore\Domain\QueryFilter\Interfaces\ForgeQueryByFilterInterface;
use ZnCore\Domain\Repository\Interfaces\CrudRepositoryInterface;
use ZnCore\Domain\Repository\Interfaces\FindOneUniqueInterface;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;

abstract class BaseRpcCrudRepository extends BaseRpcRepository implements CrudRepositoryInterface, ForgeQueryByFilterInterface, FindOneUniqueInterface
{

    abstract public function methodPrefix(): string;

    public function count(Query $query = null): int
    {
        $query = $this->forgeQuery($query);
        $query->limit(1);
        $requestEntity = $this->_all($query);
        return $requestEntity->getMetaItem('totalCount');
    }

    public function all(Query $query = null): Enumerable
    {
        $query = $this->forgeQuery($query);
        $responseEntity = $this->_all($query);
        $collection = $this
            ->getEntityManager()
            ->createEntityCollection($this->getEntityClass(), $responseEntity->getResult());
        return $collection;
    }

    protected function _all(Query $query = null): RpcResponseEntity {
        $requestEntity = $this->createRequest('all');
        $responseEntity = $this->sendRequestByEntity($requestEntity);
        return $responseEntity;
    }

    /*protected function getDataProvider(Query $query = null): DataProviderInterface
    {
        $requestEntity = $this->createRequest('all');
        $responseEntity = $this->sendRequestByEntity($requestEntity);
        $dataProvider = new DataProvider($this, $query);

        dd($responseEntity->getMeta());

    }*/

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
