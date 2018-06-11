<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\User;

class RegisterUserCommand
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getTitle(): ?UserTitle
    {
        return $this->title;
    }

    public function setTitle(?UserTitle $title): void
    {
        $this->title = $title;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): void
    {
        $this->birthday = $birthday;
    }

    public function getBilling(): ?UpdateUserAddressCommand
    {
        return $this->billing;
    }

    public function setBilling(?UpdateUserAddressCommand $billing): void
    {
        $this->billing = $billing;
    }

    public function getShipping(): ?UpdateUserAddressCommand
    {
        return $this->shipping;
    }

    public function setShipping(?UpdateUserAddressCommand $shipping): void
    {
        $this->shipping = $shipping;
    }
}
