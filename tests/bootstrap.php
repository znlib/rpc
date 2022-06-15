<?php

//\ZnCore\Base\Libs\DotEnv\DotEnv::init();



$container = new \Illuminate\Container\Container();
$znCore = new \ZnSandbox\Sandbox\App\Libs\ZnCore($container);
$znCore->init();

/** @var \ZnSandbox\Sandbox\App\Interfaces\AppInterface $appFactory */
$appFactory = $container->get(\ZnTool\Test\Libs\TestApp::class);
$appFactory->setBundles([
    
]);
$appFactory->init();

/*$znCore->loadBundles([
    
]);*/
