<?php

namespace ZnLib\Rpc\Domain\Services;

use App\Modules\Partner\Domain\Interfaces\Services\PartnerIpServiceInterface;
use Exception;
use Illuminate\Container\Container;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use ZnBundle\Rbac\Domain\Interfaces\ManagerServiceInterface;
use ZnBundle\User\Domain\Interfaces\Entities\IdentityEntityInterface;
use ZnBundle\User\Domain\Interfaces\Services\AuthServiceInterface;
use ZnCore\Base\Enums\Http\HttpStatusCodeEnum;
use ZnCore\Base\Exceptions\ForbiddenException;
use ZnCore\Base\Exceptions\NotFoundException;
use ZnCore\Base\Exceptions\UnauthorizedException;
use ZnCore\Domain\Exceptions\UnprocessibleEntityException;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnCore\Domain\Helpers\ValidationHelper;
use ZnLib\Rpc\Domain\Entities\HandlerEntity;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Enums\HttpHeaderEnum;
use ZnLib\Rpc\Domain\Enums\RpcErrorCodeEnum;
use ZnLib\Rpc\Domain\Enums\RpcVersionEnum;
use ZnLib\Rpc\Domain\Exceptions\MethodNotFoundException;
use ZnLib\Rpc\Domain\Interfaces\Repositories\ProcedureConfigRepositoryInterface;
use ZnLib\Rpc\Domain\Interfaces\Services\ProcedureServiceInterface;
use ZnLib\Rpc\Domain\Libs\ResponseFormatter;
use ZnLib\Rpc\Rpc\Interfaces\RpcAuthInterface;

class ProcedureService implements ProcedureServiceInterface
{

    private $container;
    private $procedureConfigRepository;
    private $meta = [];
    private $logger;
    private $responseFormatter;
    private $authPartnerService;
    private $partnerIpService;
    private $rbacManager;

    public function __construct(
        Container $container,
        ProcedureConfigRepositoryInterface $procedureConfigRepository,
        LoggerInterface $logger,
        ResponseFormatter $responseFormatter,
        AuthServiceInterface $authPartnerService,
        ManagerServiceInterface $rbacManager,
        PartnerIpServiceInterface $partnerIpService = null
    )
    {
        $this->container = $container;
        $this->procedureConfigRepository = $procedureConfigRepository;
        $this->logger = $logger;
        $this->responseFormatter = $responseFormatter;
        $this->authPartnerService = $authPartnerService;
        $this->partnerIpService = $partnerIpService;
        $this->rbacManager = $rbacManager;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    private function getHandlerEntityByMethod(string $method): HandlerEntity
    {
        try {
            $handlerEntity = $this->procedureConfigRepository->getHandlerByName($method);
            $action = $handlerEntity->getMethod();
        } catch (Exception $exception) {
            $handlerParams = explode(".", $method);
            $controller = $handlerParams[0];
            $action = isset($handlerParams[1]) ? $handlerParams[1] : "";
            $handlerEntity = $this->procedureConfigRepository->getHandlerByName($controller);
        }

        $handlerEntity->setMethod($action);
        return $handlerEntity;
    }

    public function run(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        if ($requestEntity->getMeta()) {
            $this->meta = $requestEntity->getMeta();
        }

        $this->validateRequest($requestEntity);

        $method = $requestEntity->getMethod();
        $handlerEntity = $this->getHandlerEntityByMethod($method);

        try {
            $result = $this->runProcedure($handlerEntity, $requestEntity);
            $responseEntity = $this->responseFormatter->forgeResultResponse($result);
        } catch (NotFoundException $e) {
            $error = $this->responseFormatter->createErrorByException($e, HttpStatusCodeEnum::NOT_FOUND);
            $responseEntity = $this->responseFormatter->forgeErrorResponseByError($error, $requestEntity->getId());
        } catch (MethodNotFoundException $e) {
            $error = $this->responseFormatter->createErrorByException($e, RpcErrorCodeEnum::METHOD_NOT_FOUND);
            $responseEntity = $this->responseFormatter->forgeErrorResponseByError($error, $requestEntity->getId());
        } catch (UnprocessibleEntityException $e) {
            $error = $this->responseFormatter->createErrorByException($e, RpcErrorCodeEnum::INVALID_PARAMS);
            $error['data'] = ValidationHelper::collectionToArray($e->getErrorCollection());
            $error['message'] = 'Parameter validation error';
            $responseEntity = $this->responseFormatter->forgeErrorResponseByError($error, $requestEntity->getId());
        } catch (UnauthorizedException $e) {
            $error = $this->responseFormatter->createErrorByException($e, HttpStatusCodeEnum::UNAUTHORIZED);
            $responseEntity = $this->responseFormatter->forgeErrorResponseByError($error, $requestEntity->getId());
        } catch (InvalidArgumentException $e) {
            $error = $this->responseFormatter->createErrorByException($e, RpcErrorCodeEnum::INVALID_PARAMS);
            $responseEntity = $this->responseFormatter->forgeErrorResponseByError($error, $requestEntity->getId());
        } catch (ForbiddenException $e) {
            $error = $this->responseFormatter->createErrorByException($e, HttpStatusCodeEnum::FORBIDDEN);
            $responseEntity = $this->responseFormatter->forgeErrorResponseByError($error, $requestEntity->getId());
        } catch (Exception $e) {
            $error = $this->responseFormatter->createErrorByException($e, RpcErrorCodeEnum::JSON_RPC_ERROR);
            $responseEntity = $this->responseFormatter->forgeErrorResponseByError($error, $requestEntity->getId());
        }

        $responseEntity->setId($requestEntity->getId());

        return $responseEntity;
        // https://www.jsonrpc.org/specification#error_object
        // http://xmlrpc-epi.sourceforge.net/specs/rfc.fault_codes.php
    }

    private function validateRequest(RpcRequestEntity $requestEntity)
    {
        if ($requestEntity->getJsonrpc() == null) {
            throw new Exception('Empty version', RpcErrorCodeEnum::INVALID_REQUEST);
        }
        if ($requestEntity->getMethod() == null) {
            throw new Exception('Empty method', RpcErrorCodeEnum::INVALID_REQUEST);
        }
        if ($requestEntity->getParams() === null) {
            throw new Exception('Empty params', RpcErrorCodeEnum::INVALID_REQUEST);
        }
        if ($requestEntity->getJsonrpc() != RpcVersionEnum::V2_0) {
            throw new Exception('Unsupported RPC version', RpcErrorCodeEnum::INVALID_REQUEST);
        }
    }

    /**
     * @param HandlerEntity $handlerEntity
     * @param string $methodName
     * @param array $params
     * @return mixed
     * @throws NotFoundException
     * @throws UnprocessibleEntityException
     */
    private function runProcedure(HandlerEntity $handlerEntity, RpcRequestEntity $requestEntity)
    {
        //$requestEntity->setMeta($this->getMeta());

        $methodName = $handlerEntity->getMethod();
        $controllerInstance = $this->container->get($handlerEntity->getClass());

        $auth = null;
        if ($controllerInstance instanceof RpcAuthInterface) {
            $auth = $controllerInstance->auth();
        }

        if ($auth) {

            if ((in_array("*", $auth)) || (in_array($methodName, $auth))) {
                $this->partnerAuthorization($requestEntity, $handlerEntity);
            }

            if ($handlerEntity->getAccess()) {

                $token = $requestEntity->getMetaItem(HttpHeaderEnum::PARTNER_AUTHORIZATION);
                /** @var IdentityEntityInterface $identity */
                $identity = $this->authPartnerService->authenticationByToken($token);

                $isCan = false;
                foreach ($handlerEntity->getAccess() as $permission) {
                    $isCan = $this->rbacManager->checkAccess($identity->getId(), $permission);
                }

                if (!$isCan) {
                    throw new ForbiddenException('Forbidden');
                }

            }
        }

        return $this->callControllerMethod($controllerInstance, $handlerEntity, $requestEntity);
    }

    private function callControllerMethod(object $controllerInstance, HandlerEntity $handlerEntity, RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        $methodName = $handlerEntity->getMethod();
        if (!method_exists($controllerInstance, $methodName)) {
            throw new MethodNotFoundException();
        }
        $this->container->bind(RpcRequestEntity::class, function () use ($requestEntity) {
            return $requestEntity;
        });
        EntityHelper::setAttributes($controllerInstance, $handlerEntity->getAttributes());
        return $this->container->call([$controllerInstance, $methodName]);
    }

    private function partnerAuthorization(RpcRequestEntity $requestEntity, HandlerEntity $handlerEntity)
    {
        $token = $requestEntity->getMetaItem(HttpHeaderEnum::PARTNER_AUTHORIZATION);
        if (empty($token)) {
            throw new UnauthorizedException("Empty token");
        }
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
    }

    protected function checkIp(RpcRequestEntity $requestEntity, IdentityEntityInterface $identity)
    {
        if ($this->partnerIpService == null) {
            return;
        }
        $ip = $requestEntity->getMetaItem('ip');
        $isAvailable = $this->partnerIpService->isAvailable($ip, $identity);
        if ($isAvailable) {
            $this->authPartnerService->setIdentity($identity);
        } else {
            throw new UnauthorizedException("Ip blocked");
        }
    }
}
