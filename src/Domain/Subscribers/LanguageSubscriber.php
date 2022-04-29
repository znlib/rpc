<?php

namespace ZnLib\Rpc\Domain\Subscribers;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ZnBundle\Language\Domain\Interfaces\Services\RuntimeLanguageServiceInterface;
use ZnCore\Domain\Traits\EntityManagerTrait;
use ZnLib\Rpc\Domain\Enums\HttpHeaderEnum;
use ZnSandbox\Sandbox\Rpc\Domain\Enums\RpcEventEnum;
use ZnSandbox\Sandbox\Rpc\Domain\Events\RpcRequestEvent;

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
        if (!empty($languageCode)) {
            $this->languageService->setLanguage($languageCode);
        }
    }
}
