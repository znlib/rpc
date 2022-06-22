<?php

namespace ZnLib\Rpc\Rpc\Serializers;

use Illuminate\Support\Collection;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use ZnCore\Domain\DataProvider\Libs\DataProvider;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Helpers\ResponseHelper;

class _______CrudSerializer implements SerializerInterface
{

    private $attributesOnly;
    private $attributesExclude;

    public function setAttributesOnly(array $attributesOnly): void
    {
        $this->attributesOnly = $attributesOnly;
    }

    public function setAttributesExclude(array $attributesExclude): void
    {
        $this->attributesExclude = $attributesExclude;
    }

    public function encode($data): RpcResponseEntity
    {
        $result = null;
        if ($data instanceof Collection) {
            $result = $this->encodeCollection($data);
        } elseif ($data instanceof DataProvider) {
            $result = $this->encodeDataProvider($data);
        } elseif (is_object($data)) {
            $result = $this->encodeEntity($data);
        } else {
            $result = $data;
        }
        return ResponseHelper::forgeRpcResponseEntity($result);
    }

    protected function encodeEntity(object $entity)
    {
        $serializer = new Serializer([new ObjectNormalizer()]);
        $array = $serializer->normalize($entity);
        $array = $this->filterEntityAttributes($array);
        return $array;
    }

    protected function filterEntityAttributes(array $array): array
    {
        if ($this->attributesOnly) {
            $newArray = [];
            foreach ($this->attributesOnly as $key) {
                if (in_array($key, $this->attributesOnly)) {
                    $newArray[$key] = $array[$key];
                }
            }
            $array = $newArray;
        }
        if ($this->attributesExclude) {
            foreach ($this->attributesExclude as $key) {
                if (in_array($key, $this->attributesExclude)) {
                    unset($array[$key]);
                }
            }
        }
        return $array;
    }

    protected function encodeCollection(Collection $collection)
    {
        $array = [];
        foreach ($collection as $entity) {
            $array[] = $this->encodeEntity($entity);
        }
        return $array;
    }

    protected function encodeDataProvider(DataProvider $dataProvider)
    {
        $body = $this->encodeCollection($dataProvider->getCollection());
        $meta = $this->encodePaginate($dataProvider);
        $response = new RpcResponseEntity();
        $response->setResult($body);
        foreach ($meta as $metaKey => $metaValue) {
            $response->addMeta($metaKey, $metaValue);
        }
        return $response;
    }

    protected function encodePaginate(DataProvider $dataProvider)
    {
        $meta['perPage'] = $dataProvider->getPageSize();
        $meta['totalCount'] = $dataProvider->getTotalCount();
        $meta['page'] = $dataProvider->getPage();
        return $meta;
    }
}
