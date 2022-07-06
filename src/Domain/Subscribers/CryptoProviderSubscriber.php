<?php

namespace ZnLib\Rpc\Domain\Subscribers;

use ZnLib\Rpc\Domain\Enums\RpcCryptoProviderStrategyEnum;
use ZnLib\Rpc\Domain\Enums\RpcEventEnum;
use ZnLib\Rpc\Domain\Events\RpcRequestEvent;
use ZnLib\Rpc\Domain\Events\RpcResponseEvent;
use ZnLib\Rpc\Domain\Interfaces\Services\SettingsServiceInterface;
use ZnLib\Rpc\Symfony4\Web\Libs\CryptoProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ZnCore\EntityManager\Traits\EntityManagerAwareTrait;

class CryptoProviderSubscriber implements EventSubscriberInterface
{

    use EntityManagerAwareTrait;

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
