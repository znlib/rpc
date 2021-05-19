<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use ZnLib\Rpc\Symfony4\Web\Controllers\DefaultController;
use ZnLib\Rpc\Symfony4\Web\Controllers\DocsController;
use ZnLib\Rpc\Symfony4\Web\Controllers\RpcController;

return function (RoutingConfigurator $routes) {
    /*$routes
        ->add('json_rpc_main_page', '/')
        ->controller([DocsController::class, 'index']);*/
    $routes
        ->add('json_rpc_docs_all', '/json-rpc')
        ->controller([DocsController::class, 'index'])
        ->methods(['GET']);
    $routes
        ->add('json_rpc_docs_view', '/json-rpc/view/{name}')
        ->controller([DocsController::class, 'view'])
        ->methods(['GET']);
    $routes
        ->add('json_rpc_docs_download', '/json-rpc/download/{name}')
        ->controller([DocsController::class, 'download'])
        ->methods(['GET']);
    $routes
        ->add('json_rpc_call_procedure', '/json-rpc')
        ->controller([RpcController::class, 'callProcedure'])
        ->methods(['POST']);
};
