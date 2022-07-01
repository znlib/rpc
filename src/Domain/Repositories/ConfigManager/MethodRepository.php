<?php

namespace ZnLib\Rpc\Domain\Repositories\ConfigManager;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Contracts\Cache\CacheInterface;
use ZnLib\Components\Store\StoreFile;
use ZnLib\Components\Store\Base\BaseFileCrudRepository;
use ZnCore\Domain\EntityManager\Interfaces\EntityManagerInterface;
use ZnCore\Domain\Query\Entities\Query;
use ZnDatabase\Eloquent\Domain\Base\BaseEloquentCrudRepository;
use ZnLib\Rpc\Domain\Entities\MethodEntity;
use ZnLib\Rpc\Domain\Interfaces\Repositories\MethodRepositoryInterface;
use ZnCore\Base\Arr\Helpers\ArrayHelper;

class MethodRepository extends BaseFileCrudRepository implements MethodRepositoryInterface
{

    const CACHE_KEY = 'rpcMethodCollection';
    
    private $cache;

    public function __construct(EntityManagerInterface $em, AdapterInterface $cache)
    {
        parent::__construct($em);
        $this->cache = $cache;
    }

    public function fileName(): string
    {
//        return __DIR__ . '/../../../../../../../fixtures/rpc_route.php';
    }

    public function getEntityClass() : string
    {
        return MethodEntity::class;
    }

    public function findOneByMethodName(string $method, int $version): MethodEntity
    {
        $query = new Query();
        $query->where('version', $version);
        $query->where('method_name', $method);
        return $this->findOne($query);
    }

    protected function getItems(): array
    {
        $cacheItem = $this->cache->getItem(self::CACHE_KEY);
        if($cacheItem->get() == null) {
            $collection = \ZnLib\Rpc\Domain\Helpers\RoutesHelper::getAllRoutes();
            $cacheItem->set($collection);
            $cacheItem->expiresAfter(60);
            $this->cache->save($cacheItem);
        }
        return $cacheItem->get();
    }
}
