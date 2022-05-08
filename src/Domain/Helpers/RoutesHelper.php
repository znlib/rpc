<?php

namespace ZnLib\Rpc\Domain\Helpers;

use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Base\Libs\Container\Helpers\ContainerHelper;
use ZnCore\Base\Libs\App\Interfaces\ConfigManagerInterface;

class RoutesHelper
{

    public static function getAllRoutes(): array {
        $collection = [];
        $routesPath = self::getRoutesPath();
        foreach ($routesPath as $file) {
            $routes = include $file;
            $collection = ArrayHelper::merge($collection, $routes);
        }
        return $collection;
    }

    private static function getRoutesPath(): array {
        $routes = self::getConfigManager()->get('rpcRoutes');
//        $routes = $_ENV['RPC_ROUTES'];
        return $routes;
    }
    
    private static function getConfigManager(): ConfigManagerInterface {
        return ContainerHelper::getContainer()->get(ConfigManagerInterface::class);
    }
}
