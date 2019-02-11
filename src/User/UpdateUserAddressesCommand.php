<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\User;

use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

/**
 * Class UpdateUserAddressesCommand
 * @package Wizaplace\SDK\User
 */
final class UpdateUserAddressesCommand
{
    /** @var UpdateUserAddressCommand */
    private $shippingAddress;

    /** @var UpdateUserAddressCommand */
    private $billingAddress;

    /** @var int */
    private $userId;

    /**
     * @return UpdateUserAddressCommand
     */
    public function getShippingAddress(): UpdateUserAddressCommand
    {
        return $this->shippingAddress;
    }

    /**
     * @param UpdateUserAddressCommand $shippingAddress
     *
     * @return UpdateUserAddressesCommand
     */
    public function setShippingAddress(UpdateUserAddressCommand $shippingAddress): self
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    /**
     * @return UpdateUserAddressCommand
     */
    public function getBillingAddress(): UpdateUserAddressCommand
    {
        return $this->billingAddress;
    }

    /**
     * @param UpdateUserAddressCommand $billingAddress
     *
     * @return UpdateUserAddressesCommand
     */
    public function setBillingAddress(UpdateUserAddressCommand $billingAddress): self
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     *
     * @return UpdateUserAddressesCommand
     */
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
        if (!isset($this->userId)) {
            throw new SomeParametersAreInvalid('Missing user ID');
        }

        if (!isset($this->shippingAddress)) {
            throw new SomeParametersAreInvalid('Missing shipping address');
        }

        if (!isset($this->billingAddress)) {
            throw new SomeParametersAreInvalid('Missing billing address');
        }
    }
}
