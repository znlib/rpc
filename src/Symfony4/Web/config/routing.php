<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use ZnLib\Rpc\Symfony4\Web\Controllers\DefaultController;
use ZnLib\Rpc\Symfony4\Web\Controllers\DocsController;
use ZnLib\Rpc\Symfony4\Web\Controllers\RpcController;

return function (RoutingConfigurator $routes) {
    $routes
        ->add('json_rpc_main_page', '/')
        ->controller([DefaultController::class, 'index']);
    $routes
        ->add('json_rpc_docs', '/json-rpc')
        ->controller([DocsController::class, 'showDocs'])
        ->methods(['GET']);

    $routes
        ->add('json_rpc_call_procedure', '/json-rpc')
        ->controller([RpcController::class, 'callProcedure'])
        ->methods(['POST']);
};
