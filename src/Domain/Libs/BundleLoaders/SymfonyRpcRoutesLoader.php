<?php

namespace ZnLib\Rpc\Domain\Libs\BundleLoaders;

use ZnCore\Base\App\Loaders\BundleLoaders\BaseLoader;
use ZnCore\Base\Arr\Helpers\ArrayHelper;

class SymfonyRpcRoutesLoader extends BaseLoader
{

    public function loadAll(array $bundles): void
    {
        $config = [];
        foreach ($bundles as $bundle) {
            $loadedConfig = $this->load($bundle);
            $config = ArrayHelper::merge($config, $loadedConfig);
        }
        $this->getConfigManager()->set('rpcRoutes', $config);
    }
}
