<?php

namespace ZnLib\Rpc\Domain\Subscribers;

use ZnSandbox\Sandbox\Rpc\Domain\Enums\RpcCryptoProviderStrategyEnum;
use ZnSandbox\Sandbox\Rpc\Domain\Enums\RpcEventEnum;
use ZnSandbox\Sandbox\Rpc\Domain\Events\RpcRequestEvent;
use ZnSandbox\Sandbox\Rpc\Domain\Events\RpcResponseEvent;
use ZnSandbox\Sandbox\Rpc\Domain\Interfaces\Services\SettingsServiceInterface;
use ZnSandbox\Sandbox\Rpc\Symfony4\Web\Libs\CryptoProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ZnCore\Domain\Traits\EntityManagerTrait;

class CryptoProviderSubscriber implements EventSubscriberInterface
{

    use EntityManagerTrait;

    private $cryptoProvider;
    private $settingsService;

    public function __construct(CryptoProviderInterface $cryptoProvider, SettingsServiceInterface $settingsService)
    {
        $this->cryptoProvider = $cryptoProvider;
        $this->settingsService = $settingsService;
    }

    public static function getSubscribedEvents()
    {
        return [
            RpcEventEnum::BEFORE_RUN_ACTION => 'onBeforeRunAction',
            RpcEventEnum::AFTER_RUN_ACTION => 'onAfterRunAction',
        ];
    }

    public function onBeforeRunAction(RpcRequestEvent $event)
    {
        //$this->cryptoProvider->verifyRequest($event->getRequestEntity());
    }

    public function onAfterRunAction(RpcResponseEvent $event)
    {
        $settingsEntity = $this->settingsService->view();
        if($settingsEntity->getCryptoProviderStrategy() == RpcCryptoProviderStrategyEnum::JSON_DSIG) {
            $this->cryptoProvider->signResponse($event->getResponseEntity());
        }
    }
}
