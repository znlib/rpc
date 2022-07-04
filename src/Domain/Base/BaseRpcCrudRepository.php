<?php

namespace ZnLib\Rpc\Domain\Base;

use ZnCore\Domain\Collection\Interfaces\Enumerable;
use ZnCore\Domain\Entity\Interfaces\EntityIdInterface;
use ZnCore\Domain\Query\Entities\Query;
use ZnCore\Domain\QueryFilter\Interfaces\ForgeQueryByFilterInterface;
use ZnCore\Domain\Repository\Interfaces\CrudRepositoryInterface;
use ZnCore\Domain\Repository\Interfaces\FindOneUniqueInterface;
use ZnCore\Domain\Repository\Traits\CrudRepositoryDeleteTrait;
use ZnCore\Domain\Repository\Traits\CrudRepositoryFindAllTrait;
use ZnCore\Domain\Repository\Traits\CrudRepositoryFindOneTrait;
use ZnCore\Domain\Repository\Traits\CrudRepositoryInsertTrait;
use ZnCore\Domain\Repository\Traits\CrudRepositoryUpdateTrait;
use ZnCore\Domain\Repository\Traits\RepositoryRelationTrait;
use ZnLib\Rpc\Domain\Helpers\RpcQueryHelper;

abstract class BaseRpcCrudRepository extends BaseRpcRepository implements CrudRepositoryInterface, ForgeQueryByFilterInterface, FindOneUniqueInterface
{

    use CrudRepositoryFindOneTrait;
    use CrudRepositoryFindAllTrait;
    use CrudRepositoryInsertTrait;
    use CrudRepositoryUpdateTrait;
    use CrudRepositoryDeleteTrait;
    use RepositoryRelationTrait;

    abstract public function methodPrefix(): string;

    public function count(Query $query = null): int
    {
        $query = $this->forgeQuery($query);
        $requestEntity = $this->createRequest('all');
        $params = RpcQueryHelper::query2RpcParams($query);
        $requestEntity->setParams($params);
        $responseEntity = $this->sendRequestByEntity($requestEntity);
//        dd($params);
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
//        $requestEntity->setParamItem('id', $id);
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

    public function forgeQueryByFilter(object $filterModel, Query $query)
    {
        // TODO: Implement forgeQueryByFilter() method.
    }

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
