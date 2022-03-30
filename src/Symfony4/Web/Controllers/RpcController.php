<?php

namespace ZnLib\Rpc\Symfony4\Web\Controllers;

use Illuminate\Container\EntryNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ZnCore\Base\Enums\Http\HttpStatusCodeEnum;
use ZnCore\Base\Exceptions\NotFoundException;
use ZnCore\Contract\User\Exceptions\ForbiddenException;
use ZnCore\Contract\User\Exceptions\UnauthorizedException;
use ZnCore\Domain\Exceptions\UnprocessibleEntityException;
use ZnCore\Domain\Helpers\ValidationHelper;
use ZnLib\Rpc\Domain\Entities\RpcRequestCollection;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseCollection;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Enums\HttpHeaderEnum;
use ZnLib\Rpc\Domain\Enums\RpcBatchModeEnum;
use ZnLib\Rpc\Domain\Enums\RpcErrorCodeEnum;
use ZnLib\Rpc\Domain\Exceptions\InvalidRequestException;
use ZnLib\Rpc\Domain\Exceptions\MethodNotFoundException;
use ZnLib\Rpc\Domain\Exceptions\ParamNotFoundException;
use ZnLib\Rpc\Domain\Helpers\RequestHelper;
use ZnLib\Rpc\Domain\Interfaces\Services\ProcedureServiceInterface;
use ZnLib\Rpc\Domain\Libs\ResponseFormatter;
use ZnLib\Rpc\Domain\Libs\RpcJsonResponse;

class RpcController
{

    protected $procedureService;
    protected $logger;
    protected $responseFormatter;
    protected $rpcJsonResponse;

    public function __construct(
        ProcedureServiceInterface $procedureService,
        LoggerInterface $logger,
        ResponseFormatter $responseFormatter,
        RpcJsonResponse $rpcJsonResponse
    )
    {
        $this->procedureService = $procedureService;
        $this->logger = $logger;
        $this->responseFormatter = $responseFormatter;
        $this->rpcJsonResponse = $rpcJsonResponse;
    }

    public function callProcedure(Request $request): Response
    {
        $requestRawData = $request->getContent();
        $requestData = json_decode($requestRawData, true);
        $isErrorParse = json_last_error();
        $this->logger->info('request', $requestData ?: []);
        if ($isErrorParse) {
            switch ($isErrorParse) {
                case JSON_ERROR_NONE: // Ошибок нет
                    $errorDescription = 'No errors';
                    break;
                case JSON_ERROR_DEPTH: // Достигнута максимальная глубина стека
                    $errorDescription = 'Maximum stack depth reached';
                    break;
                case JSON_ERROR_STATE_MISMATCH: // Некорректные разряды или несоответствие режимов
                    $errorDescription = 'Incorrect digits or mode mismatch';
                    break;
                case JSON_ERROR_CTRL_CHAR: // Некорректный управляющий символ
                    $errorDescription = 'Invalid control character';
                    break;
                case JSON_ERROR_SYNTAX: // Синтаксическая ошибка, некорректный JSON
                    $errorDescription = 'Syntax error, invalid JSON';
                    break;
                case JSON_ERROR_UTF8: // Некорректные символы UTF-8, возможно неверно закодирован
                    $errorDescription = 'Incorrect UTF-8 characters, possibly incorrectly encoded';
                    break;
                default: // Неизвестная ошибка
                    $errorDescription = 'Unknown error';
                    break;
            }

            $responseEntity = $this->responseFormatter->forgeErrorResponse(RpcErrorCodeEnum::SERVER_ERROR_INVALID_REQUEST, "Invalid request. Parse JSON error! {$errorDescription}");
            $responseCollection = new RpcResponseCollection();
            $responseCollection->add($responseEntity);
            $batchMode = RpcBatchModeEnum::SINGLE;
        } elseif (empty($requestData)) {
            $responseEntity = $this->responseFormatter->forgeErrorResponse(RpcErrorCodeEnum::SERVER_ERROR_INVALID_REQUEST, "Invalid request. Empty request!");
            $responseCollection = new RpcResponseCollection();
            $responseCollection->add($responseEntity);
            $batchMode = RpcBatchModeEnum::SINGLE;
        } else {
            $isBatchRequest = RequestHelper::isBatchRequest($requestData);
            $batchMode = $isBatchRequest ? RpcBatchModeEnum::BATCH : RpcBatchModeEnum::SINGLE;
            $requestCollection = RequestHelper::createRequestCollection($requestData);
            $responseCollection = $this->handleData($requestCollection);
        }
        return $this->rpcJsonResponse->send($responseCollection, $batchMode);
    }

    protected function handleData(RpcRequestCollection $requestCollection): RpcResponseCollection
    {
        $responseCollection = new RpcResponseCollection();
        foreach ($requestCollection->getCollection() as $requestEntity) {
            /** @var RpcRequestEntity $requestEntity */
            $requestEntity->addMeta(HttpHeaderEnum::IP, $_SERVER['REMOTE_ADDR']);
            $responseEntity = $this->callOneProcedure($requestEntity);
            $responseCollection->add($responseEntity);
        }
        return $responseCollection;
    }

    protected function callOneProcedure(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        try {
            $responseEntity = $this->procedureService->run($requestEntity);
        } catch (NotFoundException $e) {
            $responseEntity = $this->responseFormatter->forgeErrorResponse(HttpStatusCodeEnum::NOT_FOUND, $e->getMessage(), null, $e);
        } catch (MethodNotFoundException $e) {
            $responseEntity = $this->responseFormatter->forgeErrorResponse($e->getCode(), $e->getMessage(), null, $e);
        } catch (UnprocessibleEntityException $e) {
            $errorData = ValidationHelper::collectionToArray($e->getErrorCollection());
            $responseEntity = $this->responseFormatter->forgeErrorResponse(RpcErrorCodeEnum::SERVER_ERROR_INVALID_PARAMS, 'Parameter validation error', $errorData, $e);
        } catch (UnauthorizedException $e) {
            $responseEntity = $this->responseFormatter->forgeErrorResponse(HttpStatusCodeEnum::UNAUTHORIZED, 'Unauthorized', null, $e);
        } catch (ParamNotFoundException $e) {
            $responseEntity = $this->responseFormatter->forgeErrorResponse($e->getCode(), $e->getMessage(), null, $e);
        } catch (ForbiddenException $e) {
            $responseEntity = $this->responseFormatter->forgeErrorResponse(HttpStatusCodeEnum::FORBIDDEN, $e->getMessage(), null, $e);
        } catch (InvalidRequestException $e) {
            $responseEntity = $this->responseFormatter->forgeErrorResponse($e->getCode(), $e->getMessage(), null, $e);
        } catch (EntryNotFoundException $e) {
            $responseEntity = $this->responseFormatter->forgeErrorResponse(RpcErrorCodeEnum::SYSTEM_ERROR, 'Server error. Bad inject dependencies in "' . $e->getMessage() . '"', null, $e);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: RpcErrorCodeEnum::APPLICATION_ERROR;
            $message = $e->getMessage() ?: 'Application error: ' . get_class($e);
            $responseEntity = $this->responseFormatter->forgeErrorResponse($code, $message, null, $e);
        }
        $responseEntity->setId($requestEntity->getId());
        return $responseEntity;
    }
}
