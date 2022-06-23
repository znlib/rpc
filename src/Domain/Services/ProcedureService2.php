<?php

namespace ZnLib\Rpc\Domain\Services;

use ZnCore\Base\Develop\Helpers\DeprecateHelper;

DeprecateHelper::hardThrow();

class ProcedureService2 extends ProcedureService
{

    public function subscribes(): array
    {
        return [
            
        ];
    }
}
