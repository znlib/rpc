<?php

namespace ZnLib\Rpc\Domain\Services;

use ZnCore\Collection\Interfaces\Enumerable;
use ZnLib\Rpc\Domain\Entities\DocEntity;
use ZnLib\Rpc\Domain\Helpers\DocContentHelper;
use ZnLib\Rpc\Domain\Interfaces\Repositories\DocsRepositoryInterface;
use ZnLib\Rpc\Domain\Interfaces\Services\DocsServiceInterface;

class DocsService implements DocsServiceInterface
{

    private $docsRepository;

    public function __construct(DocsRepositoryInterface $docsRepository)
    {
        $this->docsRepository = $docsRepository;
    }

    public function findOneByName(string $name): DocEntity
    {
        return $this->docsRepository->findOneByName($name);
    }

    public function findAll(): Enumerable
    {
        return $this->docsRepository->findAll();
    }

    public function loadByName(string $name): string
    {
        $docsHtml = $this->docsRepository->loadByName($name);
        $docsHtml = DocContentHelper::prepareHtml($docsHtml);
        return $docsHtml;
    }
}
