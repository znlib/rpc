<?php

namespace ZnLib\Rpc\Domain\Base;

use ZnCore\Collection\Interfaces\Enumerable;
use ZnDomain\Query\Entities\Query;
use ZnDomain\QueryFilter\Interfaces\ForgeQueryByFilterInterface;
use ZnDomain\QueryFilter\Traits\ForgeQueryFilterTrait;
use ZnDomain\QueryFilter\Traits\QueryFilterTrait;
use ZnDomain\Repository\Interfaces\CrudRepositoryInterface;
use ZnDomain\Repository\Interfaces\FindOneUniqueInterface;
use ZnDomain\Repository\Traits\CrudRepositoryDeleteTrait;
use ZnDomain\Repository\Traits\CrudRepositoryFindAllTrait;
use ZnDomain\Repository\Traits\CrudRepositoryFindOneTrait;
use ZnDomain\Repository\Traits\CrudRepositoryInsertTrait;
use ZnDomain\Repository\Traits\CrudRepositoryUpdateTrait;
use ZnDomain\Repository\Traits\RepositoryRelationTrait;
use ZnDomain\Entity\Interfaces\EntityIdInterface;
use ZnLib\Rpc\Domain\Helpers\RpcQueryHelper;

abstract class BaseRpcCrudRepository extends BaseRpcRepository implements CrudRepositoryInterface, ForgeQueryByFilterInterface, FindOneUniqueInterface
{

    use CrudRepositoryFindOneTrait;
    use CrudRepositoryFindAllTrait;
    use CrudRepositoryInsertTrait;
    use CrudRepositoryUpdateTrait;
    use CrudRepositoryDeleteTrait;
    use RepositoryRelationTrait;
    use ForgeQueryFilterTrait;

    abstract public function methodPrefix(): string;

    public function count(Query $query = null): int
    {
        $query = $this->forgeQuery($query);
        $requestEntity = $this->createRequest('all');
        $params = RpcQueryHelper::query2RpcParams($query);
        $requestEntity->setParams($params);
        $responseEntity = $this->sendRequestByEntity($requestEntity);
        return $responseEntity->getMetaItem('totalCount');
    }

    protected function findBy(Query $query = null): Enumerable
    {
        $requestEntity = $this->createRequest('all');
        $params = RpcQueryHelper::query2RpcParams($query);
        $requestEntity->setParams($params);
        $responseEntity = $this->sendRequestByEntity($requestEntity);
        $collection = $this->mapperDecodeCollection($responseEntity->getResult() ?: []);

        /*$collection = $this
            ->getEntityManager()
            ->createEntityCollection($this->getEntityClass(), $responseEntity->getResult() ?: []);*/
        return $collection;
    }

    public function findOneById($id, Query $query = null): EntityIdInterface
    {
        $requestEntity = $this->createRequest('oneById');
        $params = RpcQueryHelper::query2RpcParams($query);
        $params['id'] = $id;
        $requestEntity->setParams($params);
        $responseEntity = $this->sendRequestByEntity($requestEntity);
        $entity = $this->mapperDecodeEntity($responseEntity->getResult() ?: []);

        /*$entity = $this
            ->getEntityManager()
            ->createEntity($this->getEntityClass(), $responseEntity->getResult() ?: []);*/
        return $entity;
    }

    /*public function findOneByUnique(UniqueInterface $entity): EntityIdInterface
    {
        // TODO: Implement findOneByUnique() method.
    }*/

    /*public function create(EntityIdInterface $entity)
    {
        // TODO: Implement create() method.
    }*/

    public function deleteByCondition(array $condition)
    {
        // TODO: Implement deleteByCondition() method.
    }

    protected function deleteByIdQuery($id): void
    {
        // TODO: Implement deleteByIdQuery() method.
    }

    protected function updateQuery($id, array $data): void
    {
        // TODO: Implement updateQuery() method.
    }

    protected function insertRaw($entity): void
    {
        // TODO: Implement insertRaw() method.
    }
}
