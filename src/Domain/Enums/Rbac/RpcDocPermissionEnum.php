<?php

namespace ZnLib\Rpc\Domain\Enums\Rbac;

use ZnCore\Enum\Interfaces\GetLabelsInterface;

class RpcDocPermissionEnum implements GetLabelsInterface
{

    const ALL = 'oRbacDocAll';
    const ONE = 'oRbacDocOne';
    const DOWNLOAD = 'oRbacDocDownload';

    public static function getLabels()
    {
        return [
            self::ALL => 'Документация RPC. Просмотр списка',
            self::ONE => 'Документация RPC. Просмотр записи',
            self::DOWNLOAD => 'Документация RPC. Скачать',
        ];
    }
}
