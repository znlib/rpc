<?php

namespace ZnLib\Rpc\Domain\Services;

use ZnSandbox\Sandbox\Rpc\Domain\Entities\MethodEntity;
use ZnSandbox\Sandbox\Rpc\Domain\Interfaces\Services\MethodServiceInterface;
use ZnCore\Base\Exceptions\NotFoundException;
use ZnCore\Domain\Base\BaseCrudService;
use ZnCore\Domain\Interfaces\Libs\EntityManagerInterface;
use ZnLib\Rpc\Domain\Exceptions\MethodNotFoundException;

class MethodService extends BaseCrudService implements MethodServiceInterface
{

    public function __construct(EntityManagerInterface $em)
    {
        $this->setEntityManager($em);
    }

    public function getEntityClass(): string
    {
        return MethodEntity::class;
    }

    public function oneByMethodName(string $methodName, int $version): MethodEntity
    {
        try {
            $methodEntity = $this->getRepository()->oneByMethodName($methodName, $version);
        } catch (NotFoundException $e) {
            if($methodName == 'fixture.import') {
                $methodEntity = $this->createFixtureMethod();
            } else {
                throw new MethodNotFoundException('Not found handler');
            }
        }
        return $methodEntity;
    }

    private function createFixtureMethod(): MethodEntity
    {
        $attributes = [
            'id' => 6,
            'method_name' => 'fixture.import',
            'version' => '1',
            'is_verify_eds' => false,
            'is_verify_auth' => false,
//          'permission_name' => 'oFixtureImport',
            'permission_name' => null,
            'handler_class' => 'ZnLib\Rpc\Rpc\Controllers\FixtureController',
            'handler_method' => 'import',
            'status_id' => 100,
        ];
        return $this->createEntity($attributes);
    }
}
