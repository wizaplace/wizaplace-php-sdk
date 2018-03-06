<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Basket;

use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

abstract class CheckoutCommand
{
    /** @var string|null */
    protected $basketId;

    /** @var int|null */
    protected $paymentId;

    /** @var bool|null */
    protected $acceptTerms;

    public function getBasketId(): ?string
    {
        return $this->basketId;
    }

    public function setBasketId(?string $basketId): void
    {
        $this->basketId = $basketId;
    }

    public function getPaymentId(): ?int
    {
        return $this->paymentId;
    }

    public function setPaymentId(?int $paymentId): void
    {
        $this->paymentId = $paymentId;
    }

    public function isAcceptTerms(): bool
    {
        return $this->acceptTerms;
    }

    public function setAcceptTerms(?bool $acceptTerms): void
    {
        $this->acceptTerms = $acceptTerms;
    }

    /**
     * @throws SomeParametersAreInvalid
     */
    public function validate(): void
    {
        if (!isset($this->basketId)) {
            throw new SomeParametersAreInvalid('Missing basket ID');
        }

        if (!isset($this->paymentId)) {
            throw new SomeParametersAreInvalid('Missing payment ID');
        }

        if (!isset($this->acceptTerms)) {
            throw new SomeParametersAreInvalid('Missing terms acceptance');
        }
    }

    public function serialize(): array
    {
        return [
            'paymentId'                 => $this->paymentId,
            'acceptTermsAndConditions'  => $this->acceptTerms,
        ];
    }
}
