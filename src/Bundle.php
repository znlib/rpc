<?php

namespace ZnLib\Rpc;

use ZnCore\Base\Libs\App\Base\BaseBundle;

class Bundle extends BaseBundle
{

    public function symfonyWeb(): array
    {
        return [
            __DIR__ . '/Symfony4/Web/config/routing.php',
        ];
    }

    public function i18next(): array
    {
        return [

        ];
    }

    public function migration(): array
    {
        return [

        ];
    }

    public function container(): array
    {
        return [
            __DIR__ . '/Domain/config/container.php',
        ];
    }
}
