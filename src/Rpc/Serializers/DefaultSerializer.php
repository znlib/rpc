<?php

namespace ZnLib\Rpc\Rpc\Serializers;

use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\DataProvider\Libs\DataProvider;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Helpers\ResponseHelper;

class DefaultSerializer implements SerializerInterface
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
        if ($data instanceof Enumerable) {
            $result = $this->encodeCollection($data);
        } elseif ($data instanceof DataProvider) {
            $result = $this->encodeDataProvider($data);
        } elseif (is_object($data)) {
            $result = $this->encodeEntity($data);
        } elseif (is_array($data)) {
            $result = $this->encodeArray($data);
        } else {
            $result = $data;
        }
        return ResponseHelper::forgeRpcResponseEntity($result);
    }

    protected function normalizers(): array
    {
        return [
            new DateTimeNormalizer(),
            new ObjectNormalizer(),
        ];
    }

    protected function encodeEntity(object $entity)
    {
        $serializer = new Serializer($this->normalizers());
        $array = $serializer->normalize($entity);
        $array = $this->filterEntityAttributes($array);
        return $array;
    }

    protected function encodeArray(array $entity)
    {
        $serializer = new Serializer($this->normalizers());
        $array = $serializer->normalize($entity);
        $array = $this->filterEntityAttributes($array);
        return $array;
    }

    protected function filterEntityAttributes(array $array): array
    {
        if ($this->attributesOnly) {
            $array = ArrayHelper::filter($array, $this->attributesOnly);
        }
        if ($this->attributesExclude) {
            foreach ($this->attributesExclude as $key) {
                ArrayHelper::removeItem($array, $key);
            }
        }
        return $array;
    }

    protected function encodeCollection(Enumerable $collection)
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

    public function decode($encodedData)
    {
        // TODO: Implement decode() method.
    }
}
