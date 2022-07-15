<?php

namespace ZnLib\Rpc\Test\Traits;

use ZnCore\Container\Helpers\ContainerHelper;
use ZnDomain\Сomponents\FileRepository\Repositories\Dynamic\DynamicFileRepository;

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
