<?php

namespace ZnLib\Rpc\Domain\Subscribers;

use ZnLib\Rpc\Domain\Enums\RpcEventEnum;
use ZnLib\Rpc\Domain\Events\RpcRequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ZnCore\Contract\User\Exceptions\UnauthorizedException;
use ZnDomain\Entity\Exceptions\NotFoundException;
use ZnDomain\EntityManager\Traits\EntityManagerAwareTrait;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;

class ApplicationAuthenticationSubscriber implements EventSubscriberInterface
{

    use EntityManagerAwareTrait;

    public static function getSubscribedEvents()
    {
        return [
            RpcEventEnum::BEFORE_RUN_ACTION => 'onBeforeRunAction',
        ];
    }

    public function onBeforeRunAction(RpcRequestEvent $event)
    {
        $requestEntity = $event->getRequestEntity();
        $methodEntity = $event->getMethodEntity();
        $this->applicationAuthentication($requestEntity);
    }

    /**
     * Аутентификация приложения
     * @param RpcRequestEntity $requestEntity
     * @throws UnauthorizedException
     */
    private function applicationAuthentication(RpcRequestEntity $requestEntity)
    {
        $apiKey = $requestEntity->getMetaItem('ApiKey');
        if ($apiKey) {
            try {
                // todo: реализовать
            } catch (NotFoundException $e) {
                throw new UnauthorizedException('Bad ApiKey or Signature');
            }
        }
    }
}
