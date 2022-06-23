<?php

namespace ZnLib\Rpc\Test\Traits;

use ZnTool\Test\Repositories\DynamicFileRepository;
use ZnCore\Base\Container\Helpers\ContainerHelper;

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
