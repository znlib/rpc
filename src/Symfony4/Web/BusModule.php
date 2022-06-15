<?php

namespace ZnLib\Rpc\Symfony4\Web;

use Illuminate\Container\Container;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use ZnCore\Base\Helpers\DeprecateHelper;
use ZnLib\Web\Symfony4\MicroApp\BaseModule;

DeprecateHelper::hardThrow();

class BusModule extends BaseModule
{

    public function configContainer(Container $container)
    {
        //todo: move to domain config "container.php"
        /*$container->bind(ServerRepository::class, function () {
            return new ServerRepository($_ENV['HOST_CONF_DIR'], new HostsRepository());
        }, true);*/
    }

    public function configRouting(RoutingConfigurator $routes)
    {
        /*$routes
            ->add('main_page', '/')
            ->controller([DefaultController::class, 'index']);

        $routes
            ->add('docs', '/json-rpc')
            ->controller([DocsController::class, 'view'])
            ->methods(['GET']);

        $routes
            ->add('call_procedure', '/json-rpc')
            ->controller([RpcController::class, 'callProcedure'])
            ->methods(['POST']);*/
    }

    /*public function configRoutes(RouteCollection $routes)
    {
        $routes->add('bus_index', new Route('/', [
            '_controller' => DefaultController::class,
            '_action' => 'index',
        ]));

        $callProcedureRoute = new Route('/json-rpc', [
            '_controller' => RpcController::class,
            '_action' => 'callProcedure',
        ]);

        $routes->add('bus_call_procedure', $callProcedureRoute);
    }*/
}
