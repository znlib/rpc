<?php

namespace ZnLib\Rpc\Domain\Libs;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use ZnCore\Base\Libs\Http\Enums\HttpMethodEnum;
use ZnCore\Base\Libs\Http\Enums\HttpStatusCodeEnum;
use ZnCore\Domain\Entity\Helpers\EntityHelper;
use ZnCore\Base\Libs\Validation\Helpers\ValidationHelper;
use ZnLib\Rest\Contract\Authorization\AuthorizationInterface;
use ZnLib\Rest\Helpers\RestResponseHelper;
use ZnLib\Rpc\Domain\Encoders\RequestEncoder;
use ZnLib\Rpc\Domain\Encoders\ResponseEncoder;
use ZnLib\Rpc\Domain\Entities\RpcRequestCollection;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseCollection;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Enums\RpcVersionEnum;
use ZnLib\Rpc\Domain\Exceptions\InvalidRpcVersionException;

class RpcClient
{

    private $guzzleClient;
    private $isStrictMode = true;
    private $accept = 'application/json';
//    private $authAgent;
    private $requestEncoder;
    private $responseEncoder;
    protected $headers = [];

    public function __construct(Client $guzzleClient, RequestEncoder $requestEncoder, ResponseEncoder $responseEncoder/*, AuthorizationInterface $authAgent = null*/)
    {
        $this->guzzleClient = $guzzleClient;
        $this->requestEncoder = $requestEncoder;
        $this->responseEncoder = $responseEncoder;
//        $this->setAuthAgent($authAgent);
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    public function getGuzzleClient(): Client
    {
        return $this->guzzleClient;
    }

    public function setGuzzleClient(Client $guzzleClient): void
    {
        $this->guzzleClient = $guzzleClient;
    }

   /* public function getAuthAgent(): ?AuthorizationInterface
    {
        return $this->authAgent;
    }

    public function setAuthAgent(AuthorizationInterface $authAgent = null)
    {
        $this->authAgent = $authAgent;
    }*/

    public function sendRequestByEntity(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        $requestEntity->setJsonrpc(RpcVersionEnum::V2_0);
        if ($requestEntity->getId() == null) {
            $requestEntity->setId(1);
        }
        ValidationHelper::validateEntity($requestEntity);
        $body = EntityHelper::toArray($requestEntity);
        $response = $this->sendRequest($body);
        return $response;
    }

    public function sendBatchRequest(RpcRequestCollection $rpcRequestCollection): RpcResponseCollection
    {
        $arrayBody = [];
        foreach ($rpcRequestCollection->getCollection() as $requestEntity) {
            $requestEntity->setJsonrpc(RpcVersionEnum::V2_0);
            $body = EntityHelper::toArray($requestEntity);
            $arrayBody[] = $this->requestEncoder->encode($body);
        }
        $response = $this->sendRawRequest($arrayBody);
        $data = RestResponseHelper::getBody($response);
        $responseCollection = new RpcResponseCollection();
        foreach ($data as $item) {
            $rpcResponse = new RpcResponseEntity();
            $item = $this->responseEncoder->decode($item);
            EntityHelper::setAttributes($rpcResponse, $item);
            $responseCollection->add($rpcResponse);
        }
        return $responseCollection;
    }

    public function sendRequest(array $body = []): RpcResponseEntity
    {
        $body = $this->requestEncoder->encode($body);
        $response = $this->sendRawRequest($body);
        if ($this->isStrictMode) {
            $this->validateResponse($response);
        }
        return $this->responseToRpcResponse($response);
    }

    private function responseToRpcResponse(ResponseInterface $response): RpcResponseEntity
    {
        $data = RestResponseHelper::getBody($response);
        $data = $this->responseEncoder->decode($data);
        $rpcResponse = new RpcResponseEntity();
        if (!is_array($data)) {
//            dd($data);
            throw new \Exception('Empty response');
        }
        EntityHelper::setAttributes($rpcResponse, $data);
        return $rpcResponse;
    }

    private function sendRawRequest(array $body = [])
    {
        $options = [
            RequestOptions::JSON => $body,
            RequestOptions::HEADERS => $this->headers,
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
//        exit($response->getBody()->getContents());
        return $response;
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
            throw new InvalidRpcVersionException();
        }
        if (version_compare($data['jsonrpc'], RpcVersionEnum::V2_0, '<')) {
            throw new InvalidRpcVersionException('Unsupported RPC version');
        }
    }
}
