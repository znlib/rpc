<?php

namespace ZnLib\Rpc\Test\Traits;

use ZnCore\Base\Enums\Http\HttpStatusCodeEnum;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Test\RpcAssert;

trait CrudRpcTestTrait
{

    use RepositoryTestTrait;

    abstract protected function baseMethod(): string;

    abstract protected function createRequest(string $login = null): RpcRequestEntity;

    abstract protected function getRpcAssert(RpcResponseEntity $response = null): RpcAssert;

    abstract protected function getExistedId(): int;

    protected function getNextId(): int
    {
        return $this->getRepository()->getTotal() + 1;
    }

    protected function getFirstId(): int
    {
        return 1;
    }

    protected function getTotalCount(): int
    {
        return $this->getRepository()->getTotal();
    }

    protected function all(array $data = [], string $login = null): RpcResponseEntity
    {
        $request = $this->createRequest($login);
        $request->setMethod($this->baseMethod() . '.all');
        $request->setParams($data);
        return $this->sendRequestByEntity($request);
    }

    protected function create(array $data, string $login = null): RpcResponseEntity
    {
        $request = $this->createRequest($login);
        $request->setMethod($this->baseMethod() . '.create');
        $request->setParams($data);
        return $this->sendRequestByEntity($request);
    }

    protected function update(array $data, string $login = null): RpcResponseEntity
    {
        $request = $this->createRequest($login);
        $request->setMethod($this->baseMethod() . '.update');
        $request->setParams($data);
        return $this->sendRequestByEntity($request);
    }

    protected function deleteById(int $id, string $login = null): RpcResponseEntity
    {
        $request = $this->createRequest($login);
        $request->setMethod($this->baseMethod() . '.delete');
        $request->setParamItem('id', $id);
        return $this->sendRequestByEntity($request);
    }

    protected function oneById(int $id, string $login = null, array $params = []): RpcResponseEntity
    {
        $request = $this->createRequest($login);
        $request->setMethod($this->baseMethod() . '.oneById');
        $request->setParamItem('id', $id);
        if($params) {
            foreach ($params as $paramKey => $paramValue) {
                $request->setParamItem($paramKey, $paramValue);
            }
        }
        return $this->sendRequestByEntity($request);
    }

    protected function assertExistsById(int $id, string $login = null)
    {
        $response = $this->oneById($id, $login);
        $expectedItem = $this->getRepository()->oneByIdAsArray($id);
        $this->getRpcAssert($response)->assertResult(['id' => $expectedItem['id']]);
    }

    protected function assertNotFoundById(int $id, string $login = null)
    {
        $response = $this->oneById($id, $login);
        $this->getRpcAssert($response)->assertNotFound();
    }

    protected function assertItem(array $data, string $login = null)
    {
        $response = $this->oneById($data['id'], $login);
        $this->getRpcAssert($response)->assertResult($data);
    }

    protected function assertAuthActions(array $arr)
    {
        foreach ($arr as $methodName => $isRequireAuth) {
            if(!is_null($isRequireAuth)) {
                $request = $this->createRequest();
                $request->setMethod($this->baseMethod() . '.' . $methodName);
                $request->setParams([]);
                $response = $this->sendRequestByEntity($request);
                if($isRequireAuth) {

                    $this->getRpcAssert($response)->assertTrue($response->getError()['code'] == HttpStatusCodeEnum::UNAUTHORIZED, 'Unauthorized required method ' . $methodName);

//                    $this->getRpcAssert($response)->assertUnauthorized(/*'Unauthorized required method'*/);
                } else {

                    $this->getRpcAssert($response)->assertTrue($response->getError()['code'] != HttpStatusCodeEnum::UNAUTHORIZED, 'authorized not required method ' . $methodName);


//                    $this->getRpcAssert($response)->assertIsResult(/*'Authorized not required method'*/);
                }
            }
        }
    }

    protected function assertCrudAuth(bool $all = null, bool $one = null, bool $create = null, bool $update = null, bool $delete = null)
    {
        $arr = [
            'all' => $all,
            'oneById' => $one,
            'create' => $create,
            'update' => $update,
            'delete' => $delete,
        ];

        $this->assertAuthActions($arr);

        /*
        if(!is_null($all)) {
            $response = $this->all();
            if($all) {
                $this->getRpcAssert($response)->assertUnauthorized();
            } else {
                $this->getRpcAssert($response)->assertIsResult();
            }
        }
        if(!is_null($one)) {
            $response = $this->oneById($this->getExistedId());
            if($one) {
                $this->getRpcAssert($response)->assertUnauthorized();
            } else {
                $this->getRpcAssert($response)->assertIsResult();
            }
        }
        if(!is_null($create)) {
            $response = $this->create([]);
            if($create) {
                $this->getRpcAssert($response)->assertUnauthorized();
            } else {
                $this->getRpcAssert($response)->assertIsResult();
            }
        }
        if(!is_null($update)) {
            $response = $this->update([]);
            if($update) {
                $this->getRpcAssert($response)->assertUnauthorized();
            } else {
                $this->getRpcAssert($response)->assertIsResult();
            }
        }
        if(!is_null($delete)) {
            $response = $this->deleteById($this->getExistedId());
            if($delete) {
                $this->getRpcAssert($response)->assertUnauthorized();
            } else {
                $this->getRpcAssert($response)->assertIsResult();
            }
        }*/
    }
}
