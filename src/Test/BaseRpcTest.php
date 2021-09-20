<?php

namespace ZnLib\Rpc\Test;

use Tests\Helpers\FixtureHelper;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Enums\HttpHeaderEnum;
use ZnLib\Rpc\Domain\Libs\ArrayAuthProvider;
use ZnLib\Rpc\Domain\Libs\RpcAuthProvider;
use ZnLib\Rpc\Domain\Libs\RpcClient;
use ZnLib\Rpc\Domain\Libs\RpcFixtureProvider;
use ZnLib\Rpc\Domain\Libs\RpcProvider;
use ZnTool\Test\Base\BaseTest;
use ZnTool\Test\Traits\AssertTrait;
use ZnTool\Test\Traits\BaseUrlTrait;
use ZnTool\Test\Traits\FixtureTrait;
use PHPUnit\Framework\TestCase;

abstract class BaseRpcTest extends TestCase
{

    use FixtureTrait;
    use BaseUrlTrait;
    use AssertTrait;

    protected $defaultPassword = 'Wwwqqq111';
    protected $defaultRpcMethod;
    protected $defaultRpcMethodVersion = 1;
//    private $fixtures = [];
    private $rpcProvider;
    private $authProvider;
//    private $fixtureProvider;

    /*public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        //$this->authProvider = new RpcAuthProvider($this->rpcProvider);
        //$this->setBaseUrl($_ENV['WEB_URL']);
        $this->addFixtures(FixtureHelper::getCommonFixtures());
    }*/
    
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->rpcProvider = $this->getRpcProvider($_ENV['API_URL']);
        /*$this->rpcProvider = new RpcProvider();
        $this->rpcProvider->getRpcClient()->setHeaders([
            'env-name' => 'test',
        ]);
        $this->rpcProvider->setDefaultRpcMethod($this->defaultRpcMethod());
        $this->rpcProvider->setDefaultRpcMethodVersion($this->defaultRpcMethodVersion());*/
        $this->authProvider = new RpcAuthProvider($this->rpcProvider);
        //$this->fixtureProvider = new RpcFixtureProvider($this->rpcProvider);
    }

    /*public function getRpcProvider(): RpcProvider
    {
        return $this->rpcProvider;
    }

    public function setRpcProvider(RpcProvider $rpcProvider): void
    {
        $this->rpcProvider = $rpcProvider;
    }

    public function getAuthProvider(): object
    {
        return $this->authProvider;
    }

    public function setAuthProvider(object $authProvider): void
    {
        $this->authProvider = $authProvider;
    }

    protected function addFixtures(array $fixtures)
    {
        $this->fixtures = ArrayHelper::merge($this->fixtures, $fixtures);
    }

    protected function setUp(): void
    {
        $this->addFixtures($this->fixtures());
        if ($this->fixtures) {
            $this->fixtureProvider->import($this->fixtures);
        }
    }*/

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
            $authorizationToken = $this->authProvider->authBy($login, $this->defaultPassword);
            $request->addMeta(HttpHeaderEnum::AUTHORIZATION, $authorizationToken);
        }
        return $request;
    }

    protected function getRpcClient(): RpcClient
    {
        return $this->getRpcProvider($this->getBaseUrl())->getRpcClient();
    }

    protected function assertSuccessAuthorization(string $login, string $password)
    {
        $response = $this->authProvider->authRequest($login, $password);
        $this->getRpcAssert($response)->assertIsResult();
        $result = $response->getResult();
        $token = $result['token'];
        $this->assertContains('bearer', $token);
    }

    /*protected function authRequest(string $login, string $password): RpcResponseEntity
    {
        $response = $this->sendRequest('authentication.getTokenByPassword', [
            'login' => $login,
            'password' => $password,
        ]);
        return $response;
    }*/

    protected function authBy(string $login, string $password): string
    {
        return $this->authProvider->authBy($login, $password);
    }

    protected function getRpcAssert(RpcResponseEntity $response = null): RpcAssert
    {
        $assert = new RpcAssert($response);
        return $assert;
    }

    protected function sendRequestByEntity(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        return $this->getRpcProvider($this->getBaseUrl())->sendRequestByEntity($requestEntity);
        //return $this->rpcProvider->sendRequestByEntity($requestEntity);
    }

    protected function sendRequest(string $method, array $params = [], array $meta = [], int $id = null): RpcResponseEntity
    {
        return$this->getRpcProvider($this->getBaseUrl())->sendRequest($method, $params, $meta, $id);
//        return $this->rpcProvider->sendRequest($method, $params, $meta, $id);
    }

    protected function printContent(RpcResponseEntity $response = null, string $filter = null)
    {
        $content = EntityHelper::toArray($response);
        if ($filter) {
            $content = $filter($content);
        }
        dd($content);
    }
}
