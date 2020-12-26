<?php

namespace ZnLib\Rpc\Test;

use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Enums\RpcErrorCodeEnum;
use ZnCore\Base\Enums\Http\HttpHeaderEnum;
use ZnCore\Base\Enums\Http\HttpStatusCodeEnum;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnLib\Rest\Helpers\RestResponseHelper;
use ZnTool\Test\Asserts\BaseAssert;
use ZnTool\Test\Helpers\RestHelper;
use Psr\Http\Message\ResponseInterface;

class RpcAssert extends BaseAssert
{

    protected $response;

    public function __construct(RpcResponseEntity $response = null)
    {
        $this->response = $response;
        $this->assertEquals('2.0', $response->getJsonrpc());

//        $this->assertEqualsBodyValue('2.0', 'jsonrpc');
//        $this->assertArrayHasKey('id', $this->getBody());
    }

    public function assertErrorCode(int $code) {
        $this->assertEquals($code, $this->response->getError()['code']);
        return $this;
    }

    public function assertErrorData(array $data) {
        $this->assertEquals([$data], $this->response->getError()['data']);
        return $this;
    }

    public function assertIsError() {
        $this->assertNotEmpty($this->response->getError());
        return $this;
    }

    public function assertIsResult() {
//        $this->assertNotEmpty($this->response->getResult());
//        $this->assertInstanceOf(RpcResponseResultEntity::class, $this->response);
        $this->assertInstanceOf(RpcResponseEntity::class, $this->response);
        return $this;
    }

    public function assertId($expected)
    {
        $this->assertEquals($expected, $this->response->getId());
    }

    public function assertResult($expectedResult)
    {
        $this->assertEquals([], $this->response->getError());
//        $this->assertIsResult();
        if(is_array($expectedResult)) {
            $this->assertArraySubset($expectedResult, $this->response->getResult());
        } else {
            $this->assertEquals($expectedResult, $this->response->getResult());
        }
    }

    public function assertErrorMessage(string $message) {
        $this->assertEquals($message, $this->response->getError()['message']);
        return $this;
    }

    public function assertNotFound(string $message) {
        $this->assertIsError();
        $this->assertErrorCode(HttpStatusCodeEnum::NOT_FOUND);
        $this->assertErrorMessage($message);
        return $this;
    }

    public function assertEqualsBodyValue($expected, string $key)
    {
        $value = $this->getBodyValue($key);
        $this->assertEquals($expected, $value);
    }

    public function assertUnprocessableEntity(array $fieldNames = [])
    {
        $this->assertIsError();
        $this->assertErrorMessage('Parameter validation error');
        $this->assertErrorCode(RpcErrorCodeEnum::INVALID_PARAMS);
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

    public function assertSuccessResponse(array $result = [], int $id = null)
    {
        $this
            ->assertIsResult()
            //->assertErrorCode(HttpStatusCodeEnum::OK)
            ->assertResult($result);
        return $this;
    }
}
