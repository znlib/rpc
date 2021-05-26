<?php

namespace ZnLib\Rpc\Test;

use GuzzleHttp\Client;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnLib\Rest\Contract\Authorization\AuthorizationInterface;
use ZnLib\Rest\Contract\Authorization\BearerAuthorization;
use ZnLib\Rpc\Domain\Encoders\RequestEncoder;
use ZnLib\Rpc\Domain\Encoders\ResponseEncoder;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Enums\HttpHeaderEnum;
use ZnLib\Rpc\Domain\Libs\RpcClient;
use ZnTool\Test\Base\BaseTest;

abstract class BaseRpcTest extends BaseTest
{

    private $restClient;
    protected $requestEncoder;
    protected $responseEncoder;
    protected $defaultPassword = 'Wwwqqq111';
    protected $defaultRpcMethod;
    protected $defaultRpcMethodVersion = 1;
    private $fixtures = [];

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->requestEncoder = new RequestEncoder();
        $this->responseEncoder = new ResponseEncoder();
        parent::__construct($name, $data, $dataName);
    }

    protected function addFixtures(array $fixtures) {
        $this->fixtures = ArrayHelper::merge($this->fixtures, $fixtures);
//        dump($this->fixtures);
    }
    
    protected function setUp(): void
    {
        $this->addFixtures($this->fixtures());
        if ($this->fixtures) {
            $response = $this->sendRequest('fixture.import', [
                'fixtures' => $this->fixtures,
            ]);
        }
    }

    protected function defaultRpcMethod(): ?string
    {
        return $this->defaultRpcMethod;
    }

    protected function defaultRpcMethodVersion(): ?int
    {
        return $this->defaultRpcMethodVersion;
    }

    protected function createRequest(string $login = null): RpcRequestEntity
    {
        $request = new RpcRequestEntity();
        if ($this->defaultRpcMethod()) {
            $request->setMethod($this->defaultRpcMethod());
        }
        if ($this->defaultRpcMethodVersion()) {
            $request->setMetaItem(HttpHeaderEnum::VERSION, $this->defaultRpcMethodVersion());
        }
        if ($login) {
            $authorizationToken = $this->authBy($login, $this->defaultPassword);
            $request->addMeta(HttpHeaderEnum::AUTHORIZATION, $authorizationToken);
        }
        return $request;
    }

    protected function assertSuccessAuthorization(string $login, string $password)
    {
        $response = $this->authRequest($login, $password);
        $this->getRpcAssert($response)->assertIsResult();
        $result = $response->getResult();
        $token = $result['token'];
        $this->assertContains('bearer', $token);
    }

    protected function authRequest(string $login, string $password): RpcResponseEntity
    {
        $response = $this->sendRequest('authentication.getTokenByPassword', [
            'login' => $login,
            'password' => $password,
        ]);
        return $response;
    }

    protected function authBy(string $login, string $password): string
    {
        $response = $this->authRequest($login, $password);
        return $response->getResult()['token'];
    }

    protected function getRpcAssert(RpcResponseEntity $response = null): RpcAssert
    {
        $assert = new RpcAssert($response);
        return $assert;
    }

    protected function sendRequestByEntity(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        if ($requestEntity->getMetaItem(HttpHeaderEnum::VERSION) == null && $this->defaultRpcMethodVersion()) {
            $requestEntity->setMetaItem(HttpHeaderEnum::VERSION, $this->defaultRpcMethodVersion());
        }

        $requestEntity->setMetaItem(HttpHeaderEnum::TIMESTAMP, date(\DateTime::ISO8601));

        return $this->getRpcClient()->sendRequestByEntity($requestEntity);
    }

    protected function sendRequest(string $method, array $params = [], array $meta = [], int $id = null): RpcResponseEntity
    {
        $request = new RpcRequestEntity();
        $request->setMethod($method);
        $request->setParams($params);
        $request->setMeta($meta);
        $request->setId($id);
        $response = $this->sendRequestByEntity($request);
        return $response;
    }

    protected function printContent(RpcResponseEntity $response = null, string $filter = null)
    {
        $content = EntityHelper::toArray($response);
        if ($filter) {
            $content = $filter($content);
        }
        dd($content);
    }

    protected function getAuthorizationContract(Client $guzzleClient): AuthorizationInterface
    {
        return new BearerAuthorization($guzzleClient);
    }

    protected function getRpcClient(): RpcClient
    {
        $guzzleClient = $this->getGuzzleClient();
        $authAgent = $this->getAuthorizationContract($guzzleClient);
        return new RpcClient($guzzleClient, $this->requestEncoder, $this->responseEncoder, $authAgent);
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
        $baseUrl = $_ENV['API_URL'];
        $baseUrl = trim($baseUrl, '/');
        return $baseUrl;
    }
}
