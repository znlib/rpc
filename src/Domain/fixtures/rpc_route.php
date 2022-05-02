<?php

return [
	'deps' => [
        'rbac_item',
    ],
	'collection' => \ZnLib\Rpc\Domain\Helpers\RoutesHelper::getAllRoutes(),
];
