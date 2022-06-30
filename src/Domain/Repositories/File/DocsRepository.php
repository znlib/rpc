<?php

namespace ZnLib\Rpc\Domain\Repositories\File;

use Illuminate\Support\Collection;
use ZnCore\Base\FileSystem\Helpers\FilePathHelper;
use ZnCore\Base\FileSystem\Helpers\FindFileHelper;
use ZnCore\Domain\Entity\Exceptions\NotFoundException;
use ZnLib\Rpc\Domain\Entities\DocEntity;
use ZnLib\Rpc\Domain\Interfaces\Repositories\DocsRepositoryInterface;
use ZnLib\Web\Html\Helpers\HtmlHelper;

class DocsRepository implements DocsRepositoryInterface
{

    public function oneByName(string $name): DocEntity
    {
        $collection = $this->findAll();
        foreach ($collection as $entity) {
            if ($entity->getName() == $name) {
                return $entity;
            }
        }
        throw new NotFoundException();
    }

    /**
     * @return Collection | DocEntity[]
     */
    public function findAll(): Collection
    {
        $dir = $this->distDirectory();
        $files = FindFileHelper::scanDir($dir);
        $collection = new Collection();
        foreach ($files as &$file) {
            if (FilePathHelper::fileExt($file) == 'html') {
                $name = str_replace('.html', '', $file);
                $fileName = $dir . '/' . $file;
                $htmlCode = file_get_contents($fileName);
                $title = HtmlHelper::getTagContent($htmlCode, 'title');
                $title = str_replace('API documentation', '', $title);
                $entity = new DocEntity();
                $entity->setName($name);
                $entity->setTitle($title);
                $entity->setFileName($fileName);
                $collection->add($entity);
            }
        }
        return $collection;
    }

    public function loadByName(string $name): string
    {
        $file = $name . '.html';
        $docsFile = $this->distDirectory() . '/' . $file;
        $docsHtml = file_get_contents($docsFile);
        return $docsHtml;
    }

    private function distDirectory(): string
    {
        $rootDirectory = __DIR__ . '/../../../../../../..';
        $docsPath = 'docs/api/dist';
        return $rootDirectory . '/' . $docsPath;
    }
}
