<?php

namespace ZnLib\Rpc\Domain;

use ZnDomain\Domain\Interfaces\DomainInterface;

class Domain implements DomainInterface
{

    public function getName()
    {
        return 'bus';
    }
}
