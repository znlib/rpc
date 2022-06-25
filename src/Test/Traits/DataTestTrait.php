<?php

namespace ZnLib\Rpc\Test\Traits;

use ZnCore\Base\Develop\Helpers\DeprecateHelper;
use ZnLib\Components\Store\StoreFile;

DeprecateHelper::hardThrow();

trait DataTestTrait
{

    abstract protected function baseDataDir(): string;

    protected function loadData(string $fileName)
    {
        $fileName = $this->baseDataDir() . '/' . $fileName;
        $storeFile = new StoreFile($fileName);
        return $storeFile->load();
    }

    protected function saveData(string $fileName, $data)
    {
        $fileName = $this->baseDataDir() . '/' . $fileName;
        $storeFile = new StoreFile($fileName);
        $storeFile->save($data);
    }
}
