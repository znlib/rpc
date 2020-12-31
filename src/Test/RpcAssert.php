<?php

namespace ZnLib\Rpc\Test;

use ZnCore\Base\Enums\Http\HttpStatusCodeEnum;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Enums\RpcErrorCodeEnum;
use ZnLib\Rpc\Domain\Enums\RpcVersionEnum;
use ZnTool\Test\Asserts\BaseAssert;
use ZnTool\Test\Helpers\RestHelper;

class RpcAssert extends BaseAssert
{

    protected $response;

    public function __construct(RpcResponseEntity $response = null)
    {
        $this->response = $response;
        $this->assertEquals(RpcVersionEnum::V2_0, $response->getJsonrpc());
    }

    public function assertErrorCode(int $code)
    {
//        $this->assertIsError();
        $this->assertEquals($code, $this->response->getError()['code']);
        return $this;
    }

    public function assertErrorData(array $data)
    {
        $this->assertIsError();
        $this->assertEquals([$data], $this->response->getError()['data']);
        return $this;
    }

    public function assertErrorMessage(string $message)
    {
//        $this->assertIsError();
        $this->assertEquals($message, $this->response->getError()['message']);
        return $this;
    }

    public function assertIsError()
    {
        $this->assertTrue($this->response->isError(), 'Response is not error');
        return $this;
    }

    public function assertIsResult()
    {
        $this->assertTrue($this->response->isSuccess(), 'Response is not success');
        return $this;
    }

    public function assertId($expected)
    {
        $this->assertEquals($expected, $this->response->getId());
    }

    public function assertResult($expectedResult)
    {
        $this->assertIsResult();
        if (is_array($expectedResult)) {
            $this->assertArraySubset($expectedResult, $this->response->getResult());
        } else {
            $this->assertEquals($expectedResult, $this->response->getResult());
        }
    }

    public function assertNotFound(string $message)
    {
        $this->assertIsError();
        $this->assertErrorCode(HttpStatusCodeEnum::NOT_FOUND);
        $this->assertErrorMessage($message);
        return $this;
    }

    public function assertUnprocessableEntity(array $fieldNames = [])
    {
        $this->assertIsError();
        $this->assertErrorMessage('Parameter validation error');
        $this->assertErrorCode(RpcErrorCodeEnum::SERVER_ERROR_INVALID_PARAMS);
        if ($fieldNames) {
            foreach ($this->response->getError()['data'] as $item) {
                if (empty($item['field']) || empty($item['message'])) {
                    $this->expectExceptionMessage('Invalid errors array!');
                }
                $expectedBody[] = $item['field'];
            }
            $this->assertEquals($fieldNames, $expectedBody);
        }
        return $this;
    }
}
