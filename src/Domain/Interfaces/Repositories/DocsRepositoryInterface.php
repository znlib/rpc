<?php

namespace ZnLib\Rpc\Domain\Interfaces\Repositories;

interface DocsRepositoryInterface
{

    public function loadByName(string $name): string;
}
