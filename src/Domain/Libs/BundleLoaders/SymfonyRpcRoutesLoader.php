<?php

namespace ZnLib\Rpc\Domain\Libs\BundleLoaders;

use ZnCore\Base\Arr\Helpers\ArrayHelper;
use ZnCore\Base\ConfigManager\Interfaces\ConfigManagerInterface;
use ZnCore\Base\App\Loaders\BundleLoaders\BaseLoader;

class SymfonyRpcRoutesLoader extends BaseLoader
{

    /*public function __construct(ConfigManagerInterface $configManager)
    {
        $this->setConfigManager($configManager);
    }*/

    public function loadAll(array $bundles): array
    {
        $config = [];
        foreach ($bundles as $bundle) {
            $loadedConfig = $this->load($bundle);
            $config = ArrayHelper::merge($config, $loadedConfig);
        }
//        $_ENV['RPC_ROUTES'] = $config;
        $this->getConfigManager()->set('rpcRoutes', $config);
        return [];
//        return $config ? ['rpcRoutes' => $config] : [];
    }
}
