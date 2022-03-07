<?php

namespace ZnLib\Rpc\Domain\Interfaces\Services;

use ZnCore\Contract\User\Interfaces\Entities\IdentityEntityInterface;

interface IpServiceInterface
{

    public function isAvailable($ip, IdentityEntityInterface $identityEntity);
}
