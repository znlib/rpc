<?php

namespace ZnLib\Rpc\Domain\Repositories\ConfigManager;

use Symfony\Contracts\Cache\CacheInterface;
use ZnCore\Base\Libs\Store\StoreFile;
use ZnCore\Domain\Base\Repositories\BaseFileCrudRepository;
use ZnCore\Domain\Interfaces\Libs\EntityManagerInterface;
use ZnCore\Domain\Libs\Query;
use ZnDatabase\Eloquent\Domain\Base\BaseEloquentCrudRepository;
use ZnSandbox\Sandbox\Rpc\Domain\Entities\MethodEntity;
use ZnSandbox\Sandbox\Rpc\Domain\Interfaces\Repositories\MethodRepositoryInterface;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;

class MethodRepository extends BaseFileCrudRepository implements MethodRepositoryInterface
{

    private $items;

    public function __construct(EntityManagerInterface $em, CacheInterface $cache)
    {
        parent::__construct($em);
    }

    public function fileName(): string
    {
//        return __DIR__ . '/../../../../../../../fixtures/rpc_route.php';
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
        return \ZnLib\Rpc\Domain\Helpers\RoutesHelper::getAllRoutes();
    }
}
