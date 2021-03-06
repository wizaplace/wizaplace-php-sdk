<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Subscription;

use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validation;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

abstract class SubscriptionUpsertData
{
    /** @var null|SubscriptionStatus */
    private $status;

    /** @var null|bool */
    private $isAutorenew;

    /** @var null|string */
    private $nextPaymentAt;

    /** @var null|string */
    private $commitmentEndAt;

    /** @var null|int */
    private $quantity;

    public function setStatus(SubscriptionStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function setIsAutorenew(bool $isAutorenew): self
    {
        $this->isAutorenew = $isAutorenew;

        return $this;
    }

    public function setNextPaymentAt(string $nextPaymentAt): self
    {
        $this->nextPaymentAt = $nextPaymentAt;

        return $this;
    }

    public function setCommitmentEndAt(string $commitmentEndAt): self
    {
        $this->commitmentEndAt = $commitmentEndAt;

        return $this;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @internal
     * @throws SomeParametersAreInvalid
     */
    public function validate(): void
    {
        $builder = Validation::createValidatorBuilder()->addMethodMapping('loadValidatorMetadata');

        if (false === static::allowsPartialData()) {
            $builder->addMethodMapping('loadNullChecksValidatorMetadata');
        }

        $violations = $builder
            ->getValidator()
            ->startContext()
            ->validate($this)
            ->getViolations();

        if (\count($violations) > 0) {
            throw new SomeParametersAreInvalid(
                'Subscription data validation failed: ' . json_encode(
                    array_map(
                        function (ConstraintViolationInterface $violation): array {
                            return [
                                'field' => $violation->getPropertyPath(),
                                'message' => $violation->getMessage(),
                            ];
                        },
                        iterator_to_array($violations)
                    )
                )
            );
        }
    }

    /**
     * @internal
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $selfValidatingProperties = [
            'status',
        ];

        foreach ($metadata->getReflectionClass()->getProperties() as $prop) {
            if (\in_array($prop->getName(), $selfValidatingProperties)) {
                $metadata->addPropertyConstraint($prop->getName(), new Valid());
            }
        }
    }

    /**
     * Adds NotNull constraints on most properties.
     *
     * @internal
     */
    public static function loadNullChecksValidatorMetadata(ClassMetadata $metadata): void
    {
        $nullableProperties = [];

        foreach ($metadata->getReflectionClass()->getProperties() as $prop) {
            if (false === \in_array($prop->getName(), $nullableProperties)) {
                $metadata->addPropertyConstraint($prop->getName(), new NotNull());
            }
        }
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->status instanceof SubscriptionStatus === true) {
            $data['status'] = $this->status->getValue();
        }

        if (\is_bool($this->isAutorenew) === true) {
            $data['isAutorenew'] = $this->isAutorenew;
        }

        if (\is_string($this->nextPaymentAt) === true) {
            $data['nextPaymentAt'] = $this->nextPaymentAt;
        }

        if (\is_string($this->commitmentEndAt) === true) {
            $data['commitmentEndAt'] = $this->commitmentEndAt;
        }

        if (\is_int($this->quantity) === true) {
            $data['quantity'] = $this->quantity;
        }

        return $data;
    }

    abstract protected static function allowsPartialData(): bool;
}
