<?php

//\ZnCore\Base\Libs\DotEnv\DotEnv::init();



$container = new \Illuminate\Container\Container();
$znCore = new \ZnCore\Base\Libs\App\Libs\ZnCore($container);
$znCore->init();

/** @var \ZnCore\Base\Libs\App\Interfaces\AppInterface $appFactory */
$appFactory = $container->get(\ZnTool\Test\Libs\TestApp::class);
$appFactory->setBundles([
    
]);
$appFactory->init();

/*$znCore->loadBundles([
    
]);*/
