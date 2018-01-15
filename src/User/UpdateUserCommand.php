<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\User;

use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

final class UpdateUserCommand
{
    /** @var int */
    private $id;

    /** @var string */
    private $email;

    /** @var string */
    private $firstName;

    /** @var string */
    private $lastName;

    /** @var UserTitle|null */
    private $title;

    /** @var \DateTimeInterface|null */
    private $birthday;

    public function getUserId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getTitle(): ?UserTitle
    {
        return $this->title;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setUserId(int $userId): self
    {
        $this->id = $userId;

        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function setTitle(?UserTitle $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * @throws SomeParametersAreInvalid
     */
    public function validate(): void
    {
        if (!isset($this->id)) {
            throw new SomeParametersAreInvalid('Missing customer ID');
        }

        if (!isset($this->email)) {
            throw new SomeParametersAreInvalid('Missing customer\'s email');
        }

        if (!isset($this->firstName)) {
            throw new SomeParametersAreInvalid('Missing customer\'s first name');
        }

        if (!isset($this->lastName)) {
            throw new SomeParametersAreInvalid('Missing customer\'s last name');
        }
    }
}
