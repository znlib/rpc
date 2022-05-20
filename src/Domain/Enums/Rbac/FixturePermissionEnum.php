<?php

namespace ZnLib\Rpc\Domain\Enums\Rbac;

use ZnCore\Contract\Enum\Interfaces\GetLabelsInterface;

class FixturePermissionEnum implements GetLabelsInterface
{

    const FIXTURE_IMPORT = 'oFixtureImport';

    public static function getLabels()
    {
        return [
            self::FIXTURE_IMPORT => 'Фикстуры. Импорт',
        ];
    }
}