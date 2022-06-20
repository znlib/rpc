<?php

namespace ZnLib\Rpc\Domain\Enums\Rbac;

use ZnCore\Base\Libs\Enum\Interfaces\GetLabelsInterface;

class PartnerTokenPermissionEnum implements GetLabelsInterface
{

    const ALL = 'oPartnerTokenAll';
    const CREATE = 'oPartnerTokenCreate';
    const DELETE = 'oPartnerTokenDelete';

    public static function getLabels()
    {
        return [
            self::ALL => 'Токен партнера. Получение списка',
            self::CREATE => 'Токен партнера. Генерация',
            self::DELETE => 'Токен партнера. Удаление',
        ];
    }
}