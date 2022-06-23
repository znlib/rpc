<?php

namespace ZnLib\Rpc\Domain\Base;

use ZnCore\Base\Libs\App\Helpers\EnvHelper;
use ZnCore\Base\Libs\EventDispatcher\Traits\EventDispatcherTrait;
use ZnCore\Domain\Domain\Interfaces\GetEntityClassInterface;
use ZnCore\Domain\EntityManager\Interfaces\EntityManagerInterface;
use ZnCore\Domain\EntityManager\Traits\EntityManagerAwareTrait;
use ZnCore\Domain\Repository\Traits\MapperTrait;
use ZnLib\Rpc\Domain\Facades\RpcClientFacade;
use ZnLib\Rpc\Domain\Libs\RpcAuthProvider;
use ZnLib\Rpc\Domain\Libs\RpcProvider;

abstract class BaseRpcRepository implements GetEntityClassInterface
{

    use EventDispatcherTrait;
    use EntityManagerAwareTrait;
    use MapperTrait;

    private $entityClassName;

    public function __construct(EntityManagerInterface $em)
    {
        $this->setEntityManager($em);
    }

    public function getEntityClass(): string
    {
        return $this->entityClassName;
    }

    abstract public function baseUrl(): string;

    public function getRpcProvider(): RpcProvider
    {
        $baseUrl = $this->baseUrl();
        $rpcProvider =
            (new RpcClientFacade(EnvHelper::getAppEnv()))
                ->createRpcProvider($baseUrl);
        $authProvider = new RpcAuthProvider($rpcProvider);
        $rpcProvider->setAuthProvider($authProvider);
        return $rpcProvider;
    }
}
