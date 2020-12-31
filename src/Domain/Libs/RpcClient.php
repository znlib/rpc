<?php

namespace ZnLib\Rpc\Domain\Libs;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use ZnCore\Base\Enums\Http\HttpHeaderEnum;
use ZnCore\Base\Enums\Http\HttpMethodEnum;
use ZnCore\Base\Enums\Http\HttpStatusCodeEnum;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnCore\Domain\Helpers\ValidationHelper;
use ZnLib\Rest\Contract\Authorization\AuthorizationInterface;
use ZnLib\Rest\Helpers\RestResponseHelper;
use ZnLib\Rpc\Domain\Entities\RpcRequestCollection;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseCollection;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Enums\RpcVersionEnum;

class RpcClient
{

    private $guzzleClient;
    private $isStrictMode = true;
    private $accept = 'application/json';
    private $authAgent;

    public function __construct(Client $guzzleClient, AuthorizationInterface $authAgent = null)
    {
        $this->guzzleClient = $guzzleClient;
        $this->setAuthAgent($authAgent);
    }

    public function getGuzzleClient(): Client
    {
        return $this->guzzleClient;
    }

    public function setGuzzleClient(Client $guzzleClient): void
    {
        $this->guzzleClient = $guzzleClient;
    }

    public function getAuthAgent(): ?AuthorizationInterface
    {
        return $this->authAgent;
    }

    public function setAuthAgent(AuthorizationInterface $authAgent = null)
    {
        $this->authAgent = $authAgent;
    }

    private function responseToRpcResponse(ResponseInterface $response): RpcResponseEntity
    {
        $data = RestResponseHelper::getBody($response);
        $rpcResponse = new RpcResponseEntity();
        if(!is_array($data)) {
//            dd($data);
            throw new \Exception('Empty response');
        }
        EntityHelper::setAttributes($rpcResponse, $data);
        return $rpcResponse;
    }

    public function sendRequestByEntity(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        $requestEntity->setJsonrpc(RpcVersionEnum::V2_0);
        if($requestEntity->getId() == null) {
            $requestEntity->setId(1);
        }
        ValidationHelper::validateEntity($requestEntity);
        $body = EntityHelper::toArray($requestEntity);
        $response = $this->sendRequest($body);
        return $response;
    }

    private function prepareRequest(array $body): array {
        $params = [];
        if(isset($body['params'])) {
            $params['body'] = $body['params'];
        }
        if(isset($body['meta'])) {
            $params['meta'] = $body['meta'];
            unset($body['meta']);
        }
        if(!empty($params)) {
            $body['params'] = $params;
        }
        return $body;
    }

    public function sendBatchRequest(RpcRequestCollection $rpcRequestCollection): RpcResponseCollection
    {
        $arrayBody = [];
        foreach ($rpcRequestCollection->getCollection() as $requestEntity) {
            $requestEntity->setJsonrpc(RpcVersionEnum::V2_0);
            $body = EntityHelper::toArray($requestEntity);
            $arrayBody[] = $this->prepareRequest($body);
        }
        $response = $this->sendRawRequest($arrayBody);
        $data = RestResponseHelper::getBody($response);
        $responseCollection = new RpcResponseCollection();
        foreach ($data as $item) {
            $rpcResponse = new RpcResponseEntity();
            EntityHelper::setAttributes($rpcResponse, $item);
            $responseCollection->add($rpcResponse);
        }
        return $responseCollection;
    }

    private function sendRawRequest(array $body = [])
    {
        $options = [
            RequestOptions::JSON => $body,
            RequestOptions::HEADERS => [],
        ];
        $options[RequestOptions::HEADERS]['Accept'] = $this->accept;
        try {
            $response = $this->guzzleClient->request(HttpMethodEnum::POST, '', $options);
        } catch (RequestException $e) {
            $response = $e->getResponse();

            if ($response == null) {
                throw new \Exception('Url not found!');
            }
        }
        return $response;
    }

    public function sendRequest(array $body = []): RpcResponseEntity
    {
        $body = $this->prepareRequest($body);
        $response = $this->sendRawRequest($body);
        if ($this->isStrictMode) {
            $this->validateResponse($response);
        }
        return $this->responseToRpcResponse($response);
    }

    private function validateResponse(ResponseInterface $response)
    {
        if ($response->getStatusCode() != HttpStatusCodeEnum::OK) {
            throw new \Exception('Status code is not 200');
        }
        $data = RestResponseHelper::getBody($response);
        if (is_string($data)) {
            throw new \Exception($data);
        }
        if (is_array($data) && empty($data['jsonrpc'])) {
            throw new \Exception(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
        if (version_compare($data['jsonrpc'], RpcVersionEnum::V2_0, '<')) {
            throw new \Exception('Unsupported RPC version');
        }
    }
}
