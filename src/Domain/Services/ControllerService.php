<?php

namespace ZnLib\Rpc\Domain\Services;

use Illuminate\Container\Container;
use Psr\Log\LoggerInterface;
use ZnBundle\Rbac\Domain\Interfaces\ManagerServiceInterface;
//use ZnBundle\User\Domain\Interfaces\Entities\IdentityEntityInterface;
use ZnCore\Contract\User\Interfaces\Entities\IdentityEntityInterface;
use ZnBundle\User\Domain\Interfaces\Services\AuthServiceInterface;
use ZnCore\Base\Exceptions\ForbiddenException;
use ZnCore\Base\Exceptions\NotFoundException;
use ZnBundle\User\Domain\Exceptions\UnauthorizedException;
use ZnCore\Base\Helpers\InstanceHelper;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnLib\Rpc\Domain\Entities\HandlerEntity;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Enums\HttpHeaderEnum;
use ZnLib\Rpc\Domain\Exceptions\MethodNotFoundException;
use ZnLib\Rpc\Domain\Interfaces\Services\ControllerServiceInterface;
use ZnLib\Rpc\Domain\Interfaces\Services\IpServiceInterface;
use ZnLib\Rpc\Rpc\Interfaces\RpcAuthInterface;
use ZnLib\Telegram\Domain\Facades\Bot;

class ControllerService implements ControllerServiceInterface
{

    private $container;
    private $logger;
    private $authPartnerService;
    private $partnerIpService;
    private $rbacManager;

    public function __construct(
        Container $container,
        LoggerInterface $logger,
        AuthServiceInterface $authPartnerService,
        ManagerServiceInterface $rbacManager,
        IpServiceInterface $partnerIpService = null
    )
    {
        $this->container = $container;
        $this->logger = $logger;
        $this->authPartnerService = $authPartnerService;
        $this->partnerIpService = $partnerIpService;
        $this->rbacManager = $rbacManager;
    }

    public function runProcedure(HandlerEntity $handlerEntity, RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        $controllerInstance = $this->container->get($handlerEntity->getClass());

        $auth = [];
        if ($controllerInstance instanceof RpcAuthInterface) {
            $auth = $controllerInstance->auth();
        }

        //if ($auth) {
            $this->checkAuthrization($auth, $handlerEntity, $requestEntity);
            $this->checkPermission($handlerEntity, $requestEntity);
        //}

        return $this->callControllerMethod($controllerInstance, $handlerEntity, $requestEntity);
    }

    private function checkAuthrization(array $auth, HandlerEntity $handlerEntity, RpcRequestEntity $requestEntity)
    {
        $token = $requestEntity->getMetaItem(HttpHeaderEnum::PARTNER_AUTHORIZATION);
        $isCheckRequired = in_array("*", $auth) || in_array($handlerEntity->getMethod(), $auth);
        if ($isCheckRequired) {
            if (empty($token)) {
                throw new UnauthorizedException("Empty token");
            }
        }
        if($token) {
            try {
                $identity = $this->authPartnerService->authenticationByToken($token);
            } catch (NotFoundException $exception) {
                throw new UnauthorizedException("Token not found");
            }
            if (!$identity instanceof IdentityEntityInterface) {
                throw new UnauthorizedException("Bad token");
            }
            if ($handlerEntity->isCheckIp()) {
                $this->checkIp($requestEntity, $identity);
            }
            $this->authPartnerService->setIdentity($identity);
        }
    }

    private function checkPermission(HandlerEntity $handlerEntity, RpcRequestEntity $requestEntity)
    {
        $permissions = $this->extractPermissions($handlerEntity);
        if ($permissions == null) {
            return;
        }
        /** @var IdentityEntityInterface $identity */
        $isGuest = $this->authPartnerService->isGuest();
        if($isGuest) {
            $userId = null;
        } else {
            $identity = $this->authPartnerService->getIdentity();
            $userId = $identity->getId();
        }
        $this->rbacManager->can($userId, $permissions);
    }

    private function extractPermissions(HandlerEntity $handlerEntity): array {
        $access = $handlerEntity->getAccess();
        if ($access == null) {
            return [];
        }
        $isAssociative = ! ArrayHelper::isIndexed($access);
        if($isAssociative) {
            $accessItem = $access[$handlerEntity->getMethod()];
            $access = [
                $accessItem
            ];
        }
        return $access;
    }

    private function callControllerMethod(object $controllerInstance, HandlerEntity $handlerEntity, RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        $methodName = $handlerEntity->getMethod();
        if (!method_exists($controllerInstance, $methodName)) {
            throw new MethodNotFoundException();
        }
        EntityHelper::setAttributes($controllerInstance, $handlerEntity->getAttributes());
        $parameters = [
            RpcRequestEntity::class => $requestEntity
        ];
        //return call_user_func([$controllerInstance, $methodName], $requestEntity);
        /*$this->container->bind(RpcRequestEntity::class, function () use ($requestEntity) {
            return $requestEntity;
        });*/
        return InstanceHelper::callMethod($controllerInstance, $methodName, $parameters);
//        return $this->container->call([$controllerInstance, $methodName], $parameters);
    }

    protected function checkIp(RpcRequestEntity $requestEntity, IdentityEntityInterface $identity)
    {
        if ($this->partnerIpService == null) {
            return;
        }
        $ip = $requestEntity->getMetaItem('ip');
        $isAvailable = $this->partnerIpService->isAvailable($ip, $identity);
        if (!$isAvailable) {
            throw new UnauthorizedException("Ip blocked");
        }
    }
}
