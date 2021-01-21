<?php

namespace ZnLib\Rpc\Domain\Interfaces\Services;

use ZnBundle\User\Domain\Interfaces\Entities\IdentityEntityInterface;

interface IpServiceInterface
{

    public function isAvailable($ip, IdentityEntityInterface $identityEntity);
}
