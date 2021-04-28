<?php

namespace ZnLib\Rpc\Domain\Interfaces\Services;

use ZnLib\Rpc\Domain\Entities\DocEntity;

interface DocsServiceInterface
{

    public function oneByName(string $name): DocEntity;
    public function loadByName(string $name): string;
}
