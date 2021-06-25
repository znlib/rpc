<?php

namespace ZnLib\Rpc\Domain\Libs;

use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;

class RpcFixtureProvider
{

    protected $rpcProvider;

    public function __construct(RpcProvider $rpcProvider)
    {
        $this->rpcProvider = $rpcProvider;
    }

    public function import(array $fixtures) {
        $request = new RpcRequestEntity();
        $request->setMethod('fixture.import');
        $request->setParams([
            'fixtures' => $fixtures,
        ]);
        $response = $this->rpcProvider->sendRequestByEntity($request);

        /*$response = $this->rpcProvider->sendRequest('fixture.import', [
            'fixtures' => $fixtures,
        ]);*/
    }
}
