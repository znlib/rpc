<?php

namespace ZnLib\Rpc\Domain\Services;

use Illuminate\Support\Collection;
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

    public function oneByName(string $name): DocEntity
    {
        return $this->docsRepository->oneByName($name);
    }
    
    public function all(): Collection
    {
        return $this->docsRepository->all();
    }

    public function loadByName(string $name): string
    {
        $docsHtml = $this->docsRepository->loadByName($name);
        $docsHtml = DocContentHelper::prepareHtml($docsHtml);
        return $docsHtml;
    }
}
