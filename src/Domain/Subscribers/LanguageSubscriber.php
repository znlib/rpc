<?php

namespace ZnLib\Rpc\Domain\Subscribers;

use ZnBundle\Language\Domain\Interfaces\Services\RuntimeLanguageServiceInterface;
use ZnSandbox\Sandbox\Rpc\Domain\Enums\RpcCryptoProviderStrategyEnum;
use ZnSandbox\Sandbox\Rpc\Domain\Enums\RpcEventEnum;
use ZnSandbox\Sandbox\Rpc\Domain\Events\RpcRequestEvent;
use ZnSandbox\Sandbox\Rpc\Domain\Events\RpcResponseEvent;
use ZnSandbox\Sandbox\Rpc\Domain\Interfaces\Services\SettingsServiceInterface;
use ZnSandbox\Sandbox\Rpc\Symfony4\Web\Libs\CryptoProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ZnCore\Domain\Traits\EntityManagerTrait;
use ZnLib\Rpc\Domain\Enums\HttpHeaderEnum;
use ZnLib\Rpc\Domain\Exceptions\InvalidRequestException;

class LanguageSubscriber implements EventSubscriberInterface
{

    use EntityManagerTrait;

    private $languageService;

    public function __construct(RuntimeLanguageServiceInterface $languageService)
    {
        $this->languageService = $languageService;
    }

    public static function getSubscribedEvents()
    {
        return [
            RpcEventEnum::BEFORE_RUN_ACTION => 'onBeforeRunAction'
        ];
    }

    public function onBeforeRunAction(RpcRequestEvent $event)
    {
        $languageCode = $event->getRequestEntity()->getMetaItem(HttpHeaderEnum::LANGUAGE);
        if(!empty($languageCode)) {
            $this->languageService->setLanguage($languageCode);
        }
    }
}
