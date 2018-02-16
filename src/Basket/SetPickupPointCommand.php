<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Basket;

use Wizaplace\SDK\Exception\SomeParametersAreInvalid;
use Wizaplace\SDK\User\UserTitle;

final class SetPickupPointCommand
{
    /** @var string */
    private $basketId;

    /** @var string */
    private $pickupPointId;

    /** @var UserTitle */
    private $title;

    /** @var string */
    private $firstName;

    /** @var string */
    private $lastName;

    /**
     * @param string $basketId the targeted basket's ID
     * @return $this
     */
    public function setBasketId(string $basketId): self
    {
        $this->basketId = $basketId;

        return $this;
    }

    /**
     * @param string $pickupPointId
     * @return $this
     */
    public function setPickupPointId(string $pickupPointId): self
    {
        $this->pickupPointId = $pickupPointId;

        return $this;
    }

    /**
     * @param UserTitle $title the recipient's title
     * @return $this
     */
    public function setTitle(UserTitle $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param string $firstName the recipient's first name
     * @return $this
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @param string $lastName the recipient's last name
     * @return $this
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getBasketId(): string
    {
        return $this->basketId;
    }

    public function getPickupPointId(): string
    {
        return $this->pickupPointId;
    }

    public function getTitle(): UserTitle
    {
        return $this->title;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function validate(): void
    {
        if (!isset($this->basketId)) {
            throw new SomeParametersAreInvalid('missing basket ID', 400);
        }

        if (!isset($this->pickupPointId)) {
            throw new SomeParametersAreInvalid('missing pickup point ID', 400);
        }

        if (!isset($this->title)) {
            throw new SomeParametersAreInvalid('missing title', 400);
        }

        if (!isset($this->firstName)) {
            throw new SomeParametersAreInvalid('missing first name', 400);
        }

        if (!isset($this->lastName)) {
            throw new SomeParametersAreInvalid('missing last name', 400);
        }
    }
}
