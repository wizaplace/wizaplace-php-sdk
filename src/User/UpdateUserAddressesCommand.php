<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\User;

use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

final class UpdateUserAddressesCommand
{
    /** @var UpdateUserAddressCommand */
    private $shippingAddress;

    /** @var UpdateUserAddressCommand */
    private $billingAddress;

    /** @var int */
    private $userId;

    public function getShippingAddress(): UpdateUserAddressCommand
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(UpdateUserAddressCommand $shippingAddress): self
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    public function getBillingAddress(): UpdateUserAddressCommand
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(UpdateUserAddressCommand $billingAddress): self
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @throws SomeParametersAreInvalid
     */
    public function validate(): void
    {
        if (is_null($this->userId)) {
            throw new SomeParametersAreInvalid('Missing user ID');
        }

        if (is_null($this->shippingAddress)) {
            throw new SomeParametersAreInvalid('Missing shipping address');
        }

        if (is_null($this->billingAddress)) {
            throw new SomeParametersAreInvalid('Missing billing address');
        }
    }
}
