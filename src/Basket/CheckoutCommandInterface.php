<?php
/**
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Basket;

interface CheckoutCommandInterface
{
    public function getBasketId(): ?string;

    public function setBasketId(?string $basketId): void;

    public function getPaymentId(): ?int;

    public function setPaymentId(?int $paymentId): void;

    public function isAcceptTerms(): bool;

    public function setAcceptTerms(?bool $acceptTerms): void;

    public function validate(): void;

    public function serialize(): array;
}
