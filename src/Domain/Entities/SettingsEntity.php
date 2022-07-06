<?php

namespace ZnLib\Rpc\Domain\Entities;

use ZnCore\Validation\Constraints\Boolean;
use ZnCore\Enum\Constraints\Enum;
use ZnLib\Rpc\Domain\Enums\RpcCryptoProviderStrategyEnum;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use ZnCore\Enum\Helpers\EnumHelper;
use ZnCore\Entity\Interfaces\UniqueInterface;
use ZnCore\Validation\Interfaces\ValidationByMetadataInterface;

class SettingsEntity implements ValidationByMetadataInterface, UniqueInterface
{

    private $cryptoProviderStrategy = 'default';
    private $waitReceiptNotification = false;
    private $requireTimestamp = false;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('cryptoProviderStrategy', new Assert\NotBlank);
        $metadata->addPropertyConstraint('cryptoProviderStrategy', new Enum([
            'class' => RpcCryptoProviderStrategyEnum::class,
        ]));
//        $metadata->addPropertyConstraint('waitReceiptNotification', new Assert\NotBlank);
        $metadata->addPropertyConstraint('waitReceiptNotification', new Boolean());
//        $metadata->addPropertyConstraint('requireTimestamp', new Assert\NotBlank);
        $metadata->addPropertyConstraint('requireTimestamp', new Boolean());
    }

    public function unique(): array
    {
        return [];
    }

    public function setCryptoProviderStrategy(string $value): void
    {
        $this->cryptoProviderStrategy = $value;
    }

    public function getCryptoProviderStrategy(): string
    {
        return $this->cryptoProviderStrategy;
    }

    public function setWaitReceiptNotification(bool $value): void
    {
        $this->waitReceiptNotification = $value;
    }

    public function getWaitReceiptNotification(): bool
    {
        return $this->waitReceiptNotification;
    }

    public function setRequireTimestamp(bool $value): void
    {
        $this->requireTimestamp = $value;
    }

    public function getRequireTimestamp(): bool
    {
        return $this->requireTimestamp;
    }
}
