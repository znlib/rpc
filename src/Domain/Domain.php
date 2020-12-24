<?php

namespace ZnLib\Rpc\Domain;

use ZnCore\Domain\Interfaces\DomainInterface;

class Domain implements DomainInterface
{

    public function getName()
    {
        return 'bus';
    }
}
