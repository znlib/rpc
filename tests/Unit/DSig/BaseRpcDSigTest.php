<?php

namespace ZnLib\Rest\Tests\Unit\DSig;

use ZnSandbox\Sandbox\Rpc\Symfony4\Web\Libs\JsonDSigCryptoProvider;
use ZnCrypt\Pki\Domain\Helpers\RsaKeyLoaderHelper;
use ZnTool\Test\Base\BaseTest;

abstract class BaseRpcDSigTest extends BaseTest
{

    protected function getDSig(): JsonDSigCryptoProvider
    {
        $keyCaStore = RsaKeyLoaderHelper::loadKeyStoreFromDirectory(__DIR__ . '/../../data/rsa/rootCa');
        $keyStoreUser = RsaKeyLoaderHelper::loadKeyStoreFromDirectory(__DIR__ . '/../../data/rsa/user');
        return new JsonDSigCryptoProvider($keyStoreUser, $keyCaStore);
    }
}
