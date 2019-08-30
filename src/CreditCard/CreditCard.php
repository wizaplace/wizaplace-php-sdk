<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */
declare(strict_types=1);

namespace Wizaplace\SDK\CreditCard;

final class CreditCard
{
    /** @var string */
    private $id;

    /** @var int */
    private $userId;

    /** @var string */
    private $brand;

    /** @var string */
    private $pan;

    /** @var string */
    private $holder;

    /** @var string */
    private $expiryMonth;

    /** @var string */
    private $expiryYear;

    /** @var string */
    private $issuer;

    /** @var string */
    private $country;

    /** @var \DateTime */
    private $createdAt;

    public function __construct(array $data)
    {
        $this->id = $data["id"];
        $this->userId = $data["userId"];
        $this->brand = $data["brand"];
        $this->pan = $data["pan"];
        $this->holder = $data["holder"];
        $this->expiryMonth = $data["expiryMonth"];
        $this->expiryYear = $data["expiryYear"];
        $this->issuer = $data["issuer"];
        $this->country = $data["country"];
        $this->createdAt = new \DateTime($data["createdAt"]);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function getPan(): string
    {
        return $this->pan;
    }

    public function getHolder(): string
    {
        return $this->holder;
    }

    public function getExpiryMonth(): string
    {
        return $this->expiryMonth;
    }

    public function getExpiryYear(): string
    {
        return $this->expiryYear;
    }

    public function getIssuer(): string
    {
        return $this->issuer;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}
