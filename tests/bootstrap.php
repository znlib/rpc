<?php

//\ZnCore\Base\Libs\DotEnv\DotEnv::init();


$container = \Illuminate\Container\Container::getInstance();
$znCore = new \ZnSandbox\Sandbox\App\Libs\ZnCore($container);
$znCore->init();

/** @var \ZnSandbox\Sandbox\App\Interfaces\AppInterface $appFactory */
$appFactory = $container->get(\ZnTool\Test\Libs\TestApp::class);
$appFactory->setBundles([
    
]);
$appFactory->init();

/*$znCore->loadBundles([
    
]);*/
