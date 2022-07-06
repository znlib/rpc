<?php

//\ZnCore\DotEnv\Domain\Libs\DotEnv::init();



$container = new \ZnCore\Container\Libs\Container();
$znCore = new \ZnCore\App\Libs\ZnCore($container);
$znCore->init();

/** @var \ZnCore\App\Interfaces\AppInterface $appFactory */
$appFactory = $container->get(\ZnTool\Test\Libs\TestApp::class);
$appFactory->setBundles([
    
]);
$appFactory->init();

/*$znCore->loadBundles([
    
]);*/
