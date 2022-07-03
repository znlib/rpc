<?php

//\ZnCore\Base\DotEnv\Domain\Libs\DotEnv::init();



$container = new \ZnCore\Base\Container\Libs\Container();
$znCore = new \ZnCore\Base\App\Libs\ZnCore($container);
$znCore->init();

/** @var \ZnCore\Base\App\Interfaces\AppInterface $appFactory */
$appFactory = $container->get(\ZnTool\Test\Libs\TestApp::class);
$appFactory->setBundles([
    
]);
$appFactory->init();

/*$znCore->loadBundles([
    
]);*/
