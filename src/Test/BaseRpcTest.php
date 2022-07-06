<?php

namespace ZnLib\Rpc\Test;

use ZnCore\Entity\Helpers\EntityHelper;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Enums\HttpHeaderEnum;
use ZnLib\Rpc\Domain\Forms\BaseRpcAuthForm;
use ZnLib\Rpc\Domain\Forms\RpcAuthByLoginForm;
use ZnLib\Rpc\Domain\Forms\RpcAuthGuestForm;
use ZnLib\Rpc\Domain\Libs\RpcClient;
use ZnTool\Test\Base\BaseTestCase;
use ZnTool\Test\Traits\AssertTrait;
use ZnTool\Test\Traits\BaseUrlTrait;
use ZnTool\Test\Traits\FixtureTrait;

abstract class BaseRpcTest extends BaseTestCase
{

    use FixtureTrait;
    use BaseUrlTrait;
    use AssertTrait;

    protected $defaultPassword = 'Wwwqqq111';
    protected $defaultRpcMethod;
    protected $defaultRpcMethodVersion = 1;
    private $rpcProvider;
//    private $authProvider;
    private $authLogin;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->rpcProvider = $this->getRpcProvider($this->getBaseUrl());
//        $this->authProvider = new RpcAuthProvider($this->rpcProvider);
    }

    protected function defaultRpcMethod(): ?string
    {
        return $this->defaultRpcMethod;
    }

    protected function defaultRpcMethodVersion(): ?int
    {
        return $this->defaultRpcMethodVersion;
    }

    /*public function setAuthProvider(object $authProvider): void
    {
        $this->authProvider = $authProvider;
    }*/

    protected function createRequest(string $login = null): RpcRequestEntity
    {
        $this->authLogin = $login;
        $request = new RpcRequestEntity();
        if ($this->defaultRpcMethod()) {
            $request->setMethod($this->defaultRpcMethod());
        }
        if ($this->defaultRpcMethodVersion()) {
            $request->setMetaItem(HttpHeaderEnum::VERSION, $this->defaultRpcMethodVersion());
        }
        /*if ($login) {
            $authorizationToken = $this->rpcProvider->getTokenByForm(new RpcAuthByLoginForm($login, $this->defaultPassword));
            //dd($authorizationToken);
//            $this->rpcProvider->authByLogin($login, $this->defaultPassword);
//            $authorizationToken = $this->authProvider->authBy($login, $this->defaultPassword);
            $request->addMeta(HttpHeaderEnum::AUTHORIZATION, $authorizationToken);
        }*/
        return $request;
    }

    protected function getRpcClient(): RpcClient
    {
        return $this->rpcProvider->getRpcClient();
    }

    protected function assertSuccessAuthorization(string $login, string $password)
    {
        $token = $this->rpcProvider->getTokenByForm(new RpcAuthByLoginForm($login, $password));

        /*$response = $this->authProvider->authRequest($login, $password);
        $this->getRpcAssert($response)->assertIsResult();
        $result = $response->getResult();
        $token = $result['token'];*/

        $this->assertStringContainsString('bearer', $token);
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
        return $this->rpcProvider->authByLogin($login, $password);
    }

    protected function getRpcAssert(RpcResponseEntity $response = null): RpcAssert
    {
        $assert = new RpcAssert($response);
        return $assert;
    }

    protected function sendRequestByEntity(RpcRequestEntity $requestEntity, ?BaseRpcAuthForm $authForm = null): RpcResponseEntity
    {
        if ($authForm) {

        } elseif ($this->authLogin) {
            $authForm = new RpcAuthByLoginForm($this->authLogin, $this->defaultPassword);
        } else {
            $authForm = new RpcAuthGuestForm();
        }
        return $this->rpcProvider->sendRequestByEntity($requestEntity, $authForm);
        //return $this->rpcProvider->sendRequestByEntity($requestEntity);
    }

    protected function sendRequest(string $method, array $params = [], array $meta = [], int $id = null): RpcResponseEntity
    {
        $request = new RpcRequestEntity();
        $request->setMethod($method);
        $request->setParams($params);
        $request->setMeta($meta);
        $request->setId($id);
        return $this->sendRequestByEntity($request);

//        return $this->getRpcProvider($this->getBaseUrl())->sendRequest($method, $params, $meta, $id);
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
