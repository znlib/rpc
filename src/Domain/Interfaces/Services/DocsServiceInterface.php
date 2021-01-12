<?php

namespace ZnLib\Rpc\Domain\Interfaces\Services;

interface DocsServiceInterface
{

    public function loadByName(string $name): string;
}
