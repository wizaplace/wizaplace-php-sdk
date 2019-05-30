<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\User;

/**
 * Class RegisterUserCommand
 * @package Wizaplace\SDK\User
 */
final class RegisterUserCommand
{
    /** @var string */
    private $email;

    /** @var string */
    private $password;

    /** @var UserTitle|null */
    private $title;

    /** @var string|null */
    private $firstName;

    /** @var string|null */
    private $lastName;

    /** @var \DateTimeInterface|null */
    private $birthday;

    /** @var UpdateUserAddressCommand|null */
    private $billing;

    /** @var UpdateUserAddressCommand|null */
    private $shipping;

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return UserTitle|null
     */
    public function getTitle(): ?UserTitle
    {
        return $this->title;
    }

    /**
     * @param UserTitle|null $title
     */
    public function setTitle(?UserTitle $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     */
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    /**
     * @param \DateTimeInterface|null $birthday
     */
    public function setBirthday(?\DateTimeInterface $birthday): void
    {
        $this->birthday = $birthday;
    }

    /**
     * @return UpdateUserAddressCommand|null
     */
    public function getBilling(): ?UpdateUserAddressCommand
    {
        return $this->billing;
    }

    /**
     * @param UpdateUserAddressCommand|null $billing
     */
    public function setBilling(?UpdateUserAddressCommand $billing): void
    {
        $this->billing = $billing;
    }

    /**
     * @return UpdateUserAddressCommand|null
     */
    public function getShipping(): ?UpdateUserAddressCommand
    {
        return $this->shipping;
    }

    /**
     * @param UpdateUserAddressCommand|null $shipping
     */
    public function setShipping(?UpdateUserAddressCommand $shipping): void
    {
        $this->shipping = $shipping;
    }
}
