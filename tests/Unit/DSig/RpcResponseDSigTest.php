<?php

namespace ZnLib\Rest\Tests\Unit\DSig;

use ZnCore\Code\Helpers\PropertyHelper;
use ZnCrypt\Base\Domain\Exceptions\FailSignatureException;
use ZnCrypt\Base\Domain\Exceptions\InvalidDigestException;
use ZnDomain\Entity\Helpers\EntityHelper;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnTool\Test\Traits\DataTestTrait;

final class RpcResponseDSigTest extends BaseRpcDSigTest
{

    use DataTestTrait;

    protected function baseDataDir(): string
    {
        return __DIR__ . '/../../data/RpcDSigTest';
    }

    public function testSignResponse()
    {
        $responseEntity = new RpcResponseEntity();
        $responseEntity->setResult([
            "token" => "bearer kQuZ4abuj5ZiDZibe2WymSeU0pGZzbRL"
        ]);
        $responseEntity->setId(1);
        $responseEntity->addMeta("Language", "ru");
        $dSig = $this->getDSig();
        $dSig->signResponse($responseEntity);
        $dSig->verifyResponse($responseEntity);
        $responseArray = EntityHelper::toArray($responseEntity);
        $expected = $this->loadData('signedResponse.json');
        $this->assertSame($expected, $responseArray);
    }

    public function testVerifyResponse()
    {
        $responseEntity = new RpcResponseEntity();
        $signedData = $this->loadData('signedResponse.json');
        PropertyHelper::setAttributes($responseEntity, $signedData);
        $dSig = $this->getDSig();
        $dSig->verifyResponse($responseEntity);
        $this->assertTrue(true);
    }

    public function testVerifyResponseFailDigest()
    {
        $responseEntity = new RpcResponseEntity();
        $signedData = $this->loadData('signedResponseFailDigest.json');
        PropertyHelper::setAttributes($responseEntity, $signedData);
        $dSig = $this->getDSig();
        $this->expectException(InvalidDigestException::class);
        $dSig->verifyResponse($responseEntity);
    }

    public function testVerifyResponseFailSignature()
    {
        $responseEntity = new RpcResponseEntity();
        $signedData = $this->loadData('signedResponseFailSignature.json');
        PropertyHelper::setAttributes($responseEntity, $signedData);
        $dSig = $this->getDSig();
        $this->expectException(FailSignatureException::class);
        $dSig->verifyResponse($responseEntity);
    }
}
