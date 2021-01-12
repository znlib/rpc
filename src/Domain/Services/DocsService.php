<?php

namespace ZnLib\Rpc\Domain\Services;

use ZnLib\Rpc\Domain\Interfaces\Repositories\DocsRepositoryInterface;
use ZnLib\Rpc\Domain\Interfaces\Services\DocsServiceInterface;

class DocsService implements DocsServiceInterface
{

    private $docsRepository;

    public function __construct(DocsRepositoryInterface $docsRepository)
    {
        $this->docsRepository = $docsRepository;
    }

    public function loadByName(string $name): string
    {
        $docsHtml = $this->docsRepository->loadByName($name);
        $docsHtml = $this->prepareHtml($docsHtml);
        return $docsHtml;
    }

    private function prepareHtml(string $docsHtml): string
    {
        $docsHtml = preg_replace('#<span class="parent">/(.+?)\s*</span>\s*/(.+?)#i', '<span class="parent">$1.</span>$2', $docsHtml);
        $docsHtml = preg_replace('#<h3 id="(.+?)" class="panel-title">/(.+?)</h3>#i', '<h3 id="$1" class="panel-title">$2</h3>', $docsHtml);
        $docsHtml = preg_replace('#<a\s+href="(.+?)">\/(.+?)<\/a>#i', '<a href="$1">$2</a>', $docsHtml);
        $docsHtml = str_replace('API documentation', '', $docsHtml);
        return $docsHtml;
    }
}
