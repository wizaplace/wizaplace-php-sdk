<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\User;

use function theodorejb\polycast\to_int;
use function theodorejb\polycast\to_string;

/**
 * Class User
 * @package Wizaplace\SDK\User
 */
final class User
{
    private const BIRTHDAY_FORMAT = 'Y-m-d';

    /** @var int */
    private $id;
    /** @var string */
    private $email;
    /** @var UserTitle|null */
    private $title;
    /** @var string */
    private $firstname;
    /** @var string */
    private $lastname;
    /** @var \DateTimeImmutable|null */
    private $birthday;
    /** @var UserAddress|null */
    private $billingAddress;
    /** @var UserAddress|null */
    private $shippingAddress;
    /** @var int|null */
    private $companyId;
    /** @var string|null */
    private $status;
    /** @var UserType|null */
    private $type;
    /** @var string|null */
    private $phone;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = to_int($data['id']);
        $this->email = to_string($data['email']);
        $this->title = empty($data['title']) ? null : new UserTitle($data['title']);
        $this->firstname = to_string($data['firstName']);
        $this->lastname = to_string($data['lastName']);
        $this->birthday = empty($data['birthday']) ? null : \DateTimeImmutable::createFromFormat(self::BIRTHDAY_FORMAT, $data['birthday']);
        $this->billingAddress = isset($data['addresses']['billing']) ? new UserAddress($data['addresses']['billing']) : null;
        $this->shippingAddress = isset($data['addresses']['shipping']) ? new UserAddress($data['addresses']['shipping']) : null;
        $this->companyId = $data['companyId'] ?? null;
        $this->status = $data['status'] ?? null;
        $this->type = empty($data['type']) ? null : new UserType($data['type']);
        $this->phone = $data['phone'] ?? null;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return UserTitle|null
     */
    public function getTitle(): ?UserTitle
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getBirthday(): ?\DateTimeImmutable
    {
        return $this->birthday;
    }

    /**
     * @return UserAddress|null
     */
    public function getBillingAddress(): ?UserAddress
    {
        return $this->billingAddress;
    }

    /**
     * @return UserAddress|null
     */
    public function getShippingAddress(): ?UserAddress
    {
        return $this->shippingAddress;
    }

    /**
     * @return int|null
     */
    public function getCompanyId(): ?int
    {
        return $this->companyId;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @return bool
     */
    public function isVendor(): bool
    {
        return $this->getCompanyId() !== null;
    }

    /**
     * @return null|string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @return UserType|null
     */
    public function getType(): ?UserType
    {
        return $this->type;
    }
}
