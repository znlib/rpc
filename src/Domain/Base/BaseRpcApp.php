<?php

namespace ZnLib\Rpc\Domain\Base;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use ZnCore\Base\Libs\App\Interfaces\ContainerConfiguratorInterface;
use ZnLib\Rpc\Domain\Subscribers\ApplicationAuthenticationSubscriber;
use ZnLib\Rpc\Domain\Subscribers\CheckAccessSubscriber;
use ZnLib\Rpc\Domain\Subscribers\CryptoProviderSubscriber;
use ZnLib\Rpc\Domain\Subscribers\LanguageSubscriber;
use ZnLib\Rpc\Domain\Subscribers\LogSubscriber;
use ZnLib\Rpc\Domain\Subscribers\RpcFirewallSubscriber;
use ZnLib\Rpc\Domain\Subscribers\TimestampSubscriber;
use ZnLib\Rpc\Symfony4\HttpKernel\RpcFramework;
use ZnSandbox\Sandbox\App\Base\BaseApp;
use ZnSandbox\Sandbox\App\Subscribers\WebDetectTestEnvSubscriber;

abstract class BaseRpcApp extends BaseApp
{

    public function appName(): string
    {
        return 'rpc';
    }

    public function subscribes(): array
    {
        return [
            WebDetectTestEnvSubscriber::class,
        ];
    }

    public function import(): array
    {
        return ['i18next', 'container', 'rbac', 'symfonyRpc'];
    }

    protected function configContainer(ContainerConfiguratorInterface $containerConfigurator): void
    {
        $containerConfigurator->singleton(HttpKernelInterface::class, RpcFramework::class);
    }

    protected function configDispatcher(EventDispatcherInterface $dispatcher): void
    {
        $dispatcher->addSubscriber($this->container->get(ApplicationAuthenticationSubscriber::class)); // Аутентификация приложения
        $dispatcher->addSubscriber($this->container->get(RpcFirewallSubscriber::class)); // Аутентификация пользователя
        $dispatcher->addSubscriber($this->container->get(CheckAccessSubscriber::class)); // Проверка прав доступа
        $dispatcher->addSubscriber($this->container->get(TimestampSubscriber::class)); // Проверка метки времени запроса и подстановка метки времени ответа
        $dispatcher->addSubscriber($this->container->get(CryptoProviderSubscriber::class)); // Проверка подписи запроса и подписание ответа
        $dispatcher->addSubscriber($this->container->get(LogSubscriber::class)); // Логирование запроса и ответа
        $dispatcher->addSubscriber($this->container->get(LanguageSubscriber::class)); // Обработка языка
    }
}
