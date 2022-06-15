<?php

namespace ZnLib\Rpc\Domain\Services;

use ZnCore\Base\Helpers\DeprecateHelper;

DeprecateHelper::hardThrow();

class ProcedureService2 extends ProcedureService
{

    public function subscribes(): array
    {
        return [
            
        ];
    }
}
