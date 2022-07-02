<?php

namespace ZnLib\Rpc\Domain\Subscribers;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use ZnUser\Authentication\Domain\Interfaces\Services\AuthServiceInterface;
use ZnCore\Contract\User\Exceptions\UnauthorizedException;
use ZnCore\Domain\Entity\Exceptions\NotFoundException;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Enums\HttpHeaderEnum;
use ZnLib\Rpc\Domain\Enums\RpcEventEnum;
use ZnLib\Rpc\Domain\Events\RpcRequestEvent;

class RpcFirewallSubscriber implements EventSubscriberInterface
{

    private $authService;
//    private $identityService;
//    private $session;
//    private $security;

    public function __construct(
        AuthServiceInterface $authService
//        IdentityServiceInterface $identityService,
//        Security $security,
//        SessionInterface $session
    )
    {
        $this->authService = $authService;
//        $this->identityService = $identityService;
//        $this->security = $security;
//        $this->session = $session;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 128],
            RpcEventEnum::BEFORE_RUN_ACTION => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RpcRequestEvent $event)
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
