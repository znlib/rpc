<?php

namespace ZnLib\Rpc\Domain\Subscribers;

use ZnLib\Rpc\Domain\Enums\RpcCryptoProviderStrategyEnum;
use ZnLib\Rpc\Domain\Enums\RpcEventEnum;
use ZnLib\Rpc\Domain\Events\RpcRequestEvent;
use ZnLib\Rpc\Domain\Events\RpcResponseEvent;
use ZnLib\Rpc\Domain\Interfaces\Services\SettingsServiceInterface;
use ZnLib\Rpc\Symfony4\Web\Libs\CryptoProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ZnCore\Base\Libs\EntityManager\Traits\EntityManagerAwareTrait;
use ZnLib\Rpc\Domain\Enums\HttpHeaderEnum;
use ZnLib\Rpc\Domain\Exceptions\InvalidRequestException;

class TimestampSubscriber implements EventSubscriberInterface
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
        $settingsEntity = $this->settingsService->view();
        if($settingsEntity->getRequireTimestamp()) {
            $timestamp = $event->getRequestEntity()->getMetaItem(HttpHeaderEnum::TIMESTAMP);
            if (empty($timestamp)) {
                throw new InvalidRequestException('Empty timestamp');
            }
        }
    }

    public function onAfterRunAction(RpcResponseEvent $event)
    {
        $settingsEntity = $this->settingsService->view();
        if($settingsEntity->getRequireTimestamp()) {
            $event->getResponseEntity()->addMeta(HttpHeaderEnum::TIMESTAMP, date(\DateTime::ISO8601));
        }
    }
}
