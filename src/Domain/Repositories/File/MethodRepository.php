<?php

namespace ZnLib\Rpc\Domain\Repositories\File;

use ZnCore\Domain\Base\Repositories\BaseFileCrudRepository;
use ZnCore\Domain\Libs\Query;
use ZnDatabase\Eloquent\Domain\Base\BaseEloquentCrudRepository;
use ZnSandbox\Sandbox\Rpc\Domain\Entities\MethodEntity;
use ZnSandbox\Sandbox\Rpc\Domain\Interfaces\Repositories\MethodRepositoryInterface;

class MethodRepository extends BaseFileCrudRepository implements MethodRepositoryInterface
{

    public function fileName(): string
    {
        return __DIR__ . '/../../../../../../../fixtures/rpc_route.php';
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
