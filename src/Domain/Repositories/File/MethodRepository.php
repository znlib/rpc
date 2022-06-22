<?php

namespace ZnLib\Rpc\Domain\Repositories\File;

use ZnCore\Base\Libs\Store\Base\BaseFileCrudRepository;
use ZnCore\Domain\Query\Entities\Query;
use ZnDatabase\Eloquent\Domain\Base\BaseEloquentCrudRepository;
use ZnLib\Rpc\Domain\Entities\MethodEntity;
use ZnLib\Rpc\Domain\Interfaces\Repositories\MethodRepositoryInterface;

class MethodRepository extends BaseFileCrudRepository implements MethodRepositoryInterface
{

    public function fileName(): string
    {
        return __DIR__ . '/../../fixtures/rpc_route.php';
    }

    public function getEntityClass() : string
    {
        return MethodEntity::class;
    }

    public function oneByMethodName(string $method, int $version): MethodEntity
    {
        $query = new Query();
        $query->where('version', $version);
        $query->where('method_name', $method);
        return $this->one($query);
    }

    protected function getItems(): array
    {
        return parent::getItems()['collection'];
    }
}
