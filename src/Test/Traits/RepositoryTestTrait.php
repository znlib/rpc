<?php

namespace ZnLib\Rpc\Test\Traits;

use Tests\Libs\DynamicFileRepository;
use ZnCore\Base\Libs\App\Helpers\ContainerHelper;

trait RepositoryTestTrait
{

    abstract protected function itemsFileName(): string;

    protected function getRepository(string $itemsFileName = null): DynamicFileRepository
    {
        /** @var DynamicFileRepository $repository */
        $repository = ContainerHelper::getContainer()->get(DynamicFileRepository::class);
        $itemsFileName = $itemsFileName ?: $this->itemsFileName();
        $repository->setFileName($itemsFileName);
        return $repository;
    }
}
