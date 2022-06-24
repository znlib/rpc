<?php

namespace ZnLib\Rpc\Domain\Base;

use Illuminate\Support\Enumerable;
use ZnCore\Domain\Entity\Interfaces\EntityIdInterface;
use ZnCore\Domain\Query\Entities\Query;
use ZnCore\Domain\QueryFilter\Interfaces\ForgeQueryByFilterInterface;
use ZnCore\Domain\Repository\Interfaces\CrudRepositoryInterface;
use ZnCore\Domain\Repository\Interfaces\FindOneUniqueInterface;
use ZnCore\Domain\Repository\Traits\CrudRepositoryDeleteTrait;
use ZnCore\Domain\Repository\Traits\CrudRepositoryFindAllTrait;
use ZnCore\Domain\Repository\Traits\CrudRepositoryFindOneTrait;
use ZnCore\Domain\Repository\Traits\CrudRepositoryUpdateTrait;
use ZnCore\Domain\Repository\Traits\RepositoryRelationTrait;

abstract class BaseRpcCrudRepository extends BaseRpcRepository implements CrudRepositoryInterface, ForgeQueryByFilterInterface, FindOneUniqueInterface
{

    use CrudRepositoryFindOneTrait;
    use CrudRepositoryFindAllTrait;
    use CrudRepositoryUpdateTrait;
    use CrudRepositoryDeleteTrait;
    use RepositoryRelationTrait;

    abstract public function methodPrefix(): string;

    public function count(Query $query = null): int
    {
        $query = $this->forgeQuery($query);
        $requestEntity = $this->createRequest('all');
        $responseEntity = $this->sendRequestByEntity($requestEntity);
        return $responseEntity->getMetaItem('totalCount');
    }

    /*public function all(Query $query = null): Enumerable
    {
        $query = $this->forgeQuery($query);
        $collection = $this->findBy($query);
        $this->loadRelations($collection, $query->getWith() ?: []);
//        $queryFilter = $this->queryFilterInstance($query);
//        $queryFilter->loadRelations($collection);
        return $collection;
    }*/

    protected function findBy(Query $query = null): Enumerable
    {
        $requestEntity = $this->createRequest('all');
        $responseEntity = $this->sendRequestByEntity($requestEntity);
        $collection = $this
            ->getEntityManager()
            ->createEntityCollection($this->getEntityClass(), $responseEntity->getResult());
        return $collection;
    }

    /*public function oneById($id, Query $query = null): EntityIdInterface
    {
        // TODO: Implement oneById() method.
    }

    public function oneByUnique(UniqueInterface $entity): EntityIdInterface
    {
        // TODO: Implement oneByUnique() method.
    }*/

    public function forgeQueryByFilter(object $filterModel, Query $query)
    {
        // TODO: Implement forgeQueryByFilter() method.
    }

    public function create(EntityIdInterface $entity)
    {
        // TODO: Implement create() method.
    }

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
}
