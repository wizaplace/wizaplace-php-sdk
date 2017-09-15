<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\User;

final class User
{
    const BIRTHDAY_FORMAT = 'd-m-Y';

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

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->email = (string) $data['email'];
        $this->title = empty($data['title']) ? null : new UserTitle($data['title']);
        $this->firstname = (string) $data['firstName'];
        $this->lastname = (string) $data['lastName'];
        $this->birthday = empty($data['birthday']) ? null : \DateTimeImmutable::createFromFormat(self::BIRTHDAY_FORMAT, $data['birthday']);
        $this->billingAddress = isset($data['addresses']['billing']) ? new UserAddress($data['addresses']['billing']) : null;
        $this->shippingAddress = isset($data['addresses']['shipping']) ? new UserAddress($data['addresses']['shipping']) : null;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTitle(): ?UserTitle
    {
        return $this->title;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getBirthday(): ?\DateTimeImmutable
    {
        return $this->birthday;
    }

    public function getBillingAddress(): ?UserAddress
    {
        return $this->billingAddress;
    }

    public function getShippingAddress(): ?UserAddress
    {
        return $this->shippingAddress;
    }
}
