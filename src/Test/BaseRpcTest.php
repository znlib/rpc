<?php

namespace ZnLib\Rpc\Test;

use ZnLib\Rpc\Domain\Encoders\RequestEncoder;
use ZnLib\Rpc\Domain\Encoders\ResponseEncoder;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Enums\RpcVersionEnum;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use ZnCore\Base\Enums\Http\HttpStatusCodeEnum;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnLib\Rest\Contract\Authorization\AuthorizationInterface;
use ZnLib\Rest\Contract\Authorization\BearerAuthorization;
use ZnLib\Rest\Contract\Client\RestClient;
use ZnLib\Rpc\Domain\Libs\RpcClient;
use ZnLib\Rest\Helpers\RestResponseHelper;
use ZnTool\Test\Asserts\RestApiAssert;
use ZnLib\Rpc\Test\RpcAssert;
use ZnTool\Test\Base\BaseRestApiTest;
use ZnTool\Test\Base\BaseTest;
use ZnTool\Test\Libs\FixtureLoader;

abstract class BaseRpcTest extends BaseTest
{

    private $restClient;
    protected $requestEncoder;
    protected $responseEncoder;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->requestEncoder = new RequestEncoder();
        $this->responseEncoder = new ResponseEncoder();
        parent::__construct($name, $data, $dataName);
    }

    protected function setUp(): void
    {
        //parent::setUp();
        if($this->fixtures()) {
            $response = $this->sendRequest('fixture.import', [
                'fixtures' => $this->fixtures(),
            ]);
        }
    }

    protected function authBy(string $login, string $password): string
    {
        $response = $this->sendRequest('authentication.getTokenByPassword', [
            'login' => $login,
            'password' => $password,
        ]);
        return $response->getResult()['token'];
    }

    protected function getRpcAssert(RpcResponseEntity $response = null): RpcAssert
    {
        $assert = new RpcAssert($response);
        return $assert;
    }

    protected function sendRequestByEntity(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
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
