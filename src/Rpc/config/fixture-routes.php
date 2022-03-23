<?php

return [
    [
        'method_name' => 'fixture.import',
        'version' => '1',
        'is_verify_eds' => false,
        'is_verify_auth' => false,
        'permission_name' => 'oFixtureImport',
        'handler_class' => 'ZnLib\Rpc\Rpc\Controllers\FixtureController',
        'handler_method' => 'import',
        'status_id' => 100,
    ],
];
