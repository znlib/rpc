<?php

namespace ZnLib\Rpc\Domain\Services;

use ZnLib\Rpc\Domain\Interfaces\Services\VersionHandlerServiceInterface;
use ZnCore\Base\Libs\EntityManager\Interfaces\EntityManagerInterface;
use ZnCore\Base\Libs\Service\Base\BaseCrudService;
use ZnLib\Rpc\Domain\Entities\VersionHandlerEntity;

class VersionHandlerService extends BaseCrudService implements VersionHandlerServiceInterface
{

    public function __construct(EntityManagerInterface $em)
    {
        $this->setEntityManager($em);
    }

    public function getEntityClass() : string
    {
        return VersionHandlerEntity::class;
    }


}

