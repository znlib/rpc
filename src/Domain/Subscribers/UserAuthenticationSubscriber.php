<?php

namespace ZnLib\Rpc\Domain\Subscribers;

use ZnLib\Rpc\Domain\Enums\RpcEventEnum;
use ZnLib\Rpc\Domain\Events\RpcRequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ZnCore\Contract\User\Exceptions\UnauthorizedException;
use ZnBundle\User\Domain\Interfaces\Services\AuthServiceInterface;
use ZnCore\Base\Exceptions\NotFoundException;
use ZnCore\Domain\Traits\EntityManagerTrait;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Enums\HttpHeaderEnum;

/**
 * Class UserAuthenticationSubscriber
 * @package ZnLib\Rpc\Domain\Subscribers
 * @deprecated 
 * @uses RpcFirewallSubscriber
 */
class UserAuthenticationSubscriber implements EventSubscriberInterface
{

    use EntityManagerTrait;

    private $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

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
        if ($methodEntity->getIsVerifyAuth()) {
            $this->userAuthentication($requestEntity);
        }
    }

    /**
     * Аутентификация пользователя
     * @param RpcRequestEntity $requestEntity
     * @throws UnauthorizedException
     */
    private function userAuthentication(RpcRequestEntity $requestEntity)
    {
        $authorization = $requestEntity->getMetaItem(HttpHeaderEnum::AUTHORIZATION);
        if (empty($authorization)) {
            throw new UnauthorizedException('Empty token');
        }
        try {
            $identity = $this->authService->authenticationByToken($authorization);
            $this->authService->setIdentity($identity);
        } catch (NotFoundException $e) {
            throw new UnauthorizedException('Bad token');
        }
    }
}
