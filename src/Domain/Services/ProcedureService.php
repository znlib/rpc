<?php

namespace ZnLib\Rpc\Domain\Services;

use Illuminate\Container\EntryNotFoundException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use ZnLib\Components\Http\Enums\HttpStatusCodeEnum;
use ZnCore\Domain\Entity\Exceptions\NotFoundException;
use ZnCore\Base\EventDispatcher\Traits\EventDispatcherTrait;
use ZnCore\Base\Instance\Libs\InstanceProvider;
use ZnCore\Base\Validation\Helpers\ErrorCollectionHelper;
use ZnCore\Contract\User\Exceptions\ForbiddenException;
use ZnCore\Contract\User\Exceptions\UnauthorizedException;
use ZnCore\Domain\QueryFilter\Exceptions\BadFilterValidateException;
use ZnCore\Base\Validation\Exceptions\UnprocessibleEntityException;
use ZnCore\Domain\Entity\Helpers\EntityHelper;
use ZnCore\Base\Validation\Helpers\ValidationHelper;
use ZnLib\Rpc\Domain\Entities\MethodEntity;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Enums\HttpHeaderEnum;
use ZnLib\Rpc\Domain\Enums\RpcErrorCodeEnum;
use ZnLib\Rpc\Domain\Enums\RpcEventEnum;
use ZnLib\Rpc\Domain\Events\RpcRequestEvent;
use ZnLib\Rpc\Domain\Events\RpcResponseEvent;
use ZnLib\Rpc\Domain\Exceptions\InvalidRequestException;
use ZnLib\Rpc\Domain\Exceptions\SystemErrorException;
use ZnLib\Rpc\Domain\Helpers\RequestHelper;
use ZnLib\Rpc\Domain\Interfaces\Services\MethodServiceInterface;
use ZnLib\Rpc\Domain\Interfaces\Services\ProcedureServiceInterface;
use ZnLib\Rpc\Domain\Libs\ResponseFormatter;
use ZnLib\Rpc\Domain\Subscribers\ApplicationAuthenticationSubscriber;
use ZnLib\Rpc\Domain\Subscribers\CheckAccessSubscriber;
use ZnLib\Rpc\Domain\Subscribers\CryptoProviderSubscriber;
use ZnLib\Rpc\Domain\Subscribers\LanguageSubscriber;
use ZnLib\Rpc\Domain\Subscribers\LogSubscriber;
use ZnLib\Rpc\Domain\Subscribers\RpcFirewallSubscriber;
use ZnLib\Rpc\Domain\Subscribers\TimestampSubscriber;

class ProcedureService implements ProcedureServiceInterface
{

    use EventDispatcherTrait;

    private $methodService;
    private $responseFormatter;
    private $instanceProvider;

    public function __construct(
        ResponseFormatter $responseFormatter,
        MethodServiceInterface $methodService,
        EventDispatcherInterface $dispatcher,
        InstanceProvider $instanceProvider
    )
    {
        set_error_handler([$this, 'errorHandler']);
        $this->responseFormatter = $responseFormatter;
        $this->methodService = $methodService;
        $this->setEventDispatcher($dispatcher);
        $this->instanceProvider = $instanceProvider;
    }

    public function subscribes_____________(): array
    {
        return [
            ApplicationAuthenticationSubscriber::class, // Аутентификация приложения
            RpcFirewallSubscriber::class, // Аутентификация пользователя
            CheckAccessSubscriber::class, // Проверка прав доступа
            TimestampSubscriber::class, // Проверка метки времени запроса и подстановка метки времени ответа
            CryptoProviderSubscriber::class, // Проверка подписи запроса и подписание ответа
            LogSubscriber::class, // Логирование запроса и ответа
            LanguageSubscriber::class, // Обработка языка
        ];
    }

    public function callOneProcedure(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        try {
            RequestHelper::validateRequest($requestEntity);

            $version = $requestEntity->getMetaItem(HttpHeaderEnum::VERSION);
            if (empty($version)) {
                throw new InvalidRequestException('Empty method version');
            }

            $methodEntity = $this->methodService->oneByMethodName($requestEntity->getMethod(), $version);
            $this->triggerBefore($requestEntity, $methodEntity);
            $parameters = [
                RpcRequestEntity::class => $requestEntity
            ];
            $responseEntity = $this->instanceProvider->callMethod($methodEntity->getHandlerClass(), [], $methodEntity->getHandlerMethod(), $parameters);
        } catch (NotFoundException $e) {
            $responseEntity = $this->responseFormatter->forgeErrorResponse(HttpStatusCodeEnum::NOT_FOUND, $e->getMessage(), EntityHelper::toArray($e), $e);
        } catch (UnprocessibleEntityException $e) {
            $responseEntity = $this->handleUnprocessibleEntityException($e);
        } catch (UnauthorizedException $e) {
            $message = $e->getMessage() ?: 'Unauthorized';
            $responseEntity = $this->responseFormatter->forgeErrorResponse(HttpStatusCodeEnum::UNAUTHORIZED, $message, EntityHelper::toArray($e), $e);
        } catch (ForbiddenException $e) {
            $responseEntity = $this->responseFormatter->forgeErrorResponse(HttpStatusCodeEnum::FORBIDDEN, $e->getMessage(), EntityHelper::toArray($e), $e);
        } catch (EntryNotFoundException $e) {
            $message = 'Server error. Bad inject dependencies in "' . $e->getMessage() . '"';
            $responseEntity = $this->responseFormatter->forgeErrorResponse(RpcErrorCodeEnum::SYSTEM_ERROR, $message, EntityHelper::toArray($e), $e);
        } catch (\Throwable $e) {
            $code = $e->getCode() ?: RpcErrorCodeEnum::APPLICATION_ERROR;
            $message = $e->getMessage() ?: 'Application error: ' . get_class($e);
            $responseEntity = $this->responseFormatter->forgeErrorResponse(intval($code), $message, null, $e);
        }
        $responseEntity->setId($requestEntity->getId());
        $this->triggerAfter($requestEntity, $responseEntity);
        return $responseEntity;
    }

    public function errorHandler($error_level, $error_message, $error_file, $error_line, $error_context)
    {
        $message = $error_message . ' in ' . $error_file . ':' . $error_line;
        throw new SystemErrorException($message);
    }

    private function triggerBefore(RpcRequestEntity $requestEntity, MethodEntity $methodEntity)
    {
        $requestEvent = new RpcRequestEvent($requestEntity, $methodEntity);
        $this->getEventDispatcher()->dispatch($requestEvent, RpcEventEnum::BEFORE_RUN_ACTION); // todo: deprecated

        $this->getEventDispatcher()->dispatch($requestEvent, KernelEvents::REQUEST);
    }

    private function triggerAfter(RpcRequestEntity $requestEntity, RpcResponseEntity $responseEntity)
    {
        $responseEvent = new RpcResponseEvent($requestEntity, $responseEntity);
        $this->getEventDispatcher()->dispatch($responseEvent, RpcEventEnum::AFTER_RUN_ACTION);
    }

    private function handleUnprocessibleEntityException(UnprocessibleEntityException $e): RpcResponseEntity
    {
        $errorData = ErrorCollectionHelper::collectionToArray($e->getErrorCollection());
        if ($e instanceof BadFilterValidateException) {
            $message = 'Filter parameter validation error';
        } else {
            $message = 'Parameter validation error';
        }
        return $this->responseFormatter->forgeErrorResponse(RpcErrorCodeEnum::SERVER_ERROR_INVALID_PARAMS, $message, $errorData);
    }
}
