<?php

namespace ZnLib\Rpc\Domain\Libs;

use GuzzleHttp\Client;
use ZnLib\Rest\Contract\Authorization\AuthorizationInterface;
use ZnLib\Rest\Contract\Authorization\BearerAuthorization;
use ZnLib\Rpc\Domain\Encoders\RequestEncoder;
use ZnLib\Rpc\Domain\Encoders\ResponseEncoder;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Enums\HttpHeaderEnum;
use ZnLib\Rpc\Domain\Forms\BaseRpcAuthForm;
use ZnLib\Rpc\Domain\Forms\RpcAuthByLoginForm;
use ZnLib\Rpc\Domain\Forms\RpcAuthByTokenForm;
use ZnLib\Rpc\Domain\Forms\RpcAuthGuestForm;
use ZnLib\Rpc\Domain\Interfaces\Encoders\RequestEncoderInterface;
use ZnLib\Rpc\Domain\Interfaces\Encoders\ResponseEncoderInterface;

class RpcProvider
{

    protected $requestEncoder;
    protected $responseEncoder;
//    protected $defaultPassword = 'Wwwqqq111';
    protected $defaultRpcMethod;
    protected $defaultRpcMethodVersion = 1;
    protected $rpcClient;
    protected $baseUrl;
    
    /** @var RpcAuthProvider */
    private $authProvider;
    private $authToken;

    public function __construct(RequestEncoderInterface $requestEncoder = null, ResponseEncoderInterface $responseEncoder = null)
    {
        $this->requestEncoder = $requestEncoder ?? new RequestEncoder();
        $this->responseEncoder = $responseEncoder ?? new ResponseEncoder();
    }

    public function getAuthProvider(): RpcAuthProvider
    {
        return $this->authProvider ?: new RpcAuthProvider($this);
    }

    public function setAuthProvider(RpcAuthProvider $authProvider): void
    {
        $this->authProvider = $authProvider;
    }

    /*protected function getAuthorizationContract(Client $guzzleClient): AuthorizationInterface
    {
        return new BearerAuthorization($guzzleClient);
    }*/

    public function getRpcClient(): RpcClient
    {
        if(empty($this->rpcClient)) {
            $guzzleClient = $this->getGuzzleClient();
//            $authAgent = $this->getAuthorizationContract($guzzleClient);
            $this->rpcClient = new RpcClient($guzzleClient, $this->requestEncoder, $this->responseEncoder/*, $authAgent*/);
        }
        return $this->rpcClient;
    }

    protected function getGuzzleClient(): Client
    {
        $config = [
            'base_uri' => $this->getBaseUrl(),
        ];
        $client = new Client($config);
        return $client;
    }

    protected function getBaseUrl(): string
    {
        if(empty($this->baseUrl)) {
            /** @todo костыль */
            $this->setBaseUrl($_ENV['RPC_URL']);
        }
        return $this->baseUrl;
    }

    public function setBaseUrl($baseUrl): void
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function getDefaultRpcMethod()
    {
        return $this->defaultRpcMethod;
    }

    public function setDefaultRpcMethod($defaultRpcMethod): void
    {
        $this->defaultRpcMethod = $defaultRpcMethod;
    }

    public function getDefaultRpcMethodVersion(): int
    {
        return $this->defaultRpcMethodVersion;
    }

    public function setDefaultRpcMethodVersion(int $defaultRpcMethodVersion): void
    {
        $this->defaultRpcMethodVersion = $defaultRpcMethodVersion;
    }

    public function prepareRequestEntity(RpcRequestEntity $requestEntity): void
    {
        if ($requestEntity->getMetaItem(HttpHeaderEnum::VERSION) == null && $this->getDefaultRpcMethodVersion()) {
            $requestEntity->setMetaItem(HttpHeaderEnum::VERSION, $this->getDefaultRpcMethodVersion());
        }
        $requestEntity->setMetaItem(HttpHeaderEnum::TIMESTAMP, date(\DateTime::ISO8601));
    }

    public function authByLogin(string $login, string $password): void {
        $this->authByForm(new RpcAuthByLoginForm($login, $password));
//        $this->authToken = $this->getAuthProvider()->authBy($login, $password);
    }

    public function authByForm(BaseRpcAuthForm $authForm): void {
        $this->authToken = $this->getTokenByForm($authForm);
    }

    public function getTokenByForm(BaseRpcAuthForm $authForm): ?string {
        $token = null;
        /*if($authForm == null && $this->authToken) {
            $requestEntity->addMeta(HttpHeaderEnum::AUTHORIZATION, $this->authToken);
        }*/
        if($authForm instanceof RpcAuthGuestForm) {
//            $requestEntity->setMetaItem(HttpHeaderEnum::AUTHORIZATION, null);
            $token = null;
        }
        if($authForm instanceof RpcAuthByLoginForm) {
            $authorizationToken = $this
                ->getAuthProvider()
                ->authBy($authForm->getLogin(), $authForm->getPassword());
//            $requestEntity->addMeta(HttpHeaderEnum::AUTHORIZATION, $authorizationToken);
            $token = $authorizationToken;
        }
        if($authForm instanceof RpcAuthByTokenForm) {
            $token = $authForm->getToken();
        }
        return $token;
    }

    protected function prepareAuth(RpcRequestEntity $requestEntity, ?BaseRpcAuthForm $authForm): void {
        if(!$authForm && $this->authToken) {
            $requestEntity->addMeta(HttpHeaderEnum::AUTHORIZATION, $this->authToken);
        } elseif($authForm) {
            $token = $this->getTokenByForm($authForm);
            $requestEntity->addMeta(HttpHeaderEnum::AUTHORIZATION, $token);
        }
    }

    public function sendRequestByEntity(RpcRequestEntity $requestEntity, ?BaseRpcAuthForm $authForm = null): RpcResponseEntity
    {
        $this->prepareRequestEntity($requestEntity);
        $this->prepareAuth($requestEntity, $authForm);
        return $this
            ->getRpcClient()
            ->sendRequestByEntity($requestEntity);
    }

    /*public function sendRequest(string $method, array $params = [], array $meta = [], int $id = null): RpcResponseEntity
    {
        $request = new RpcRequestEntity();
        $request->setMethod($method);
        $request->setParams($params);
        $request->setMeta($meta);
        $request->setId($id);
        $response = $this->sendRequestByEntity($request);
        return $response;
    }*/
}
