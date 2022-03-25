<?php

namespace ZnLib\Rpc\Test;

use Psr\Http\Message\ResponseInterface;
use ZnCore\Base\Enums\Http\HttpStatusCodeEnum;
use ZnCore\Base\Helpers\DeprecateHelper;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
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

    public function assertNotFound(string $message = null)
    {
        $this->assertError(HttpStatusCodeEnum::NOT_FOUND, $message);
        return $this;
    }

    public function assertForbidden(string $message = null)
    {
        $this->assertError(HttpStatusCodeEnum::FORBIDDEN, $message);
        return $this;
    }

    public function assertUnauthorized(string $message = null)
    {
        $this->assertError(HttpStatusCodeEnum::UNAUTHORIZED, $message);
        return $this;
    }

    public function assertError(int $code, string $message = null)
    {
        $this->assertErrorCode($code);
        if ($message) {
            $this->assertErrorMessage($message);
        }
        return $this;
    }

    public function assertErrorCode(int $code)
    {
//        $this->assertIsError();
        $this->assertEquals($code, $this->response->getError()['code']);
        return $this;
    }

    public function assertErrorData(array $data)
    {
        DeprecateHelper::softThrow();
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

    public function assertIsError(string $message = 'Response is not error')
    {
        $this->assertTrue($this->response->isError(), $message);
        return $this;
    }

    public function assertIsResult(string $message = 'Response is not success')
    {
        $this->assertTrue($this->response->isSuccess(), $message);
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

    public function assertCollectionSize(int $expected)
    {
        $this->assertCount($expected, $this->response->getResult());
    }

    public function assertCollection($data)
    {
        $this->assertResult($data);
        $this->assertCollectionSize(count($data));
    }

    public function assertCollectionItemsById(array $ids)
    {
        $this->assertIsResult();
        $this->assertCollectionSize(count($ids));

        $actualIds = ArrayHelper::getColumn($this->response->getResult(), 'id');
        sort($ids);
        sort($actualIds);
        $this->assertEquals($ids, $actualIds);
    }

    private function assertCollectionCount(int $expected)
    {
        $this->assertIsResult();
        $this->assertCount($expected, $this->response->getResult());
        $this->assertEquals($expected, $this->response->getMetaItem('perPage'));
//        $this->assertEquals($expected, $this->response->getMetaItem('totalCount'));
    }

    public function assertPagination(int $totalCount = null, int $count, int $pageSize = null, int $page = 1)
    {

        if ($totalCount !== null) {
            $this->assertEquals($totalCount, $this->response->getMetaItem('totalCount'));
        }
//        if($count) {
        $this->assertCollectionCount($count);
//        }
        if ($pageSize !== null) {
            $this->assertEquals($pageSize, $this->response->getMetaItem('perPage'));
        }

        $this->assertEquals($page, $this->response->getMetaItem('page'));
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

    public function assertUnprocessableEntityErrors(array $errors)
    {
        $this->assertIsError();
        $this->assertErrorMessage('Parameter validation error');
        $this->assertErrorCode(RpcErrorCodeEnum::SERVER_ERROR_INVALID_PARAMS);
        $this->assertEquals($errors, $this->response->getError()['data']);
        return $this;
    }
}
