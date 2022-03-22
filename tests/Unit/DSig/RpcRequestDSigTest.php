<?php

namespace ZnLib\Rest\Tests\Unit\DSig;

use ZnLib\Rpc\Test\Traits\DataTestTrait;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnCrypt\Base\Domain\Exceptions\FailSignatureException;
use ZnCrypt\Base\Domain\Exceptions\InvalidDigestException;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;

final class RpcRequestDSigTest extends BaseRpcDSigTest
{

    use DataTestTrait;

    protected function baseDataDir(): string
    {
        return __DIR__ . '/../../data/RpcDSigTest';
    }

    public function testSignRequest()
    {
        $requestEntity = new RpcRequestEntity();
        $requestEntity->setMethod('user.oneById');
        $requestEntity->setParamItem('id', 123);
        $requestEntity->setId(1);
        $requestEntity->addMeta("Language", "ru");
        $dSig = $this->getDSig();
        $dSig->signRequest($requestEntity);
        $dSig->verifyRequest($requestEntity);
        $requestArray = EntityHelper::toArray($requestEntity);
        $expected = $this->loadData('signedRequest.json');
        $this->assertSame($expected, $requestArray);
    }

    public function testVerifyRequest()
    {
        $requestEntity = new RpcRequestEntity();
        $signedData = $this->loadData('signedRequest.json');
        EntityHelper::setAttributes($requestEntity, $signedData);
        $dSig = $this->getDSig();
        $dSig->verifyRequest($requestEntity);
        $this->assertTrue(true);
    }

    public function testVerifyRequestFailDigest()
    {
        $requestEntity = new RpcRequestEntity();
        $signedData = $this->loadData('signedRequestFailDigest.json');
        EntityHelper::setAttributes($requestEntity, $signedData);
        $dSig = $this->getDSig();
        $this->expectException(InvalidDigestException::class);
        $dSig->verifyRequest($requestEntity);
    }

    public function testVerifyRequestFailSignature()
    {
        $requestEntity = new RpcRequestEntity();
        $signedData = $this->loadData('signedRequestFailSignature.json');
        EntityHelper::setAttributes($requestEntity, $signedData);
        $dSig = $this->getDSig();
        $this->expectException(FailSignatureException::class);
        $dSig->verifyRequest($requestEntity);
    }
}
