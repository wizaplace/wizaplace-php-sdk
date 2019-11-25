<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\User;

use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

/**
 * Class UpdateUserCommand
 * @package Wizaplace\SDK\User
 */
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

    /** @var string|null */
    private $currencyCode;

    /** @var string|null */
    private $phone;

    /** @var string|null */
    private $externalIdentifier;

    /** @var bool|null */
    private $isProfessional;

    /** @var string|null */
    private $intraEuropeanCommunityVAT;

    /** @var string|null */
    private $company;

    /** @var string|null */
    private $function;

    /** @return string|null */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @return int
     */
    public function getUserId(): int
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
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return UserTitle|null
     */
    public function getTitle(): ?UserTitle
    {
        return $this->title;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    /**
     * @return string|null
     */
    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }

    /**
     * @param int $userId
     *
     * @return UpdateUserCommand
     */
    public function setUserId(int $userId): self
    {
        $this->id = $userId;

        return $this;
    }

    /**
     * @param string|null $phone
     *
     * @return UpdateUserCommand
     */
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @param string $email
     *
     * @return UpdateUserCommand
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @param string $firstName
     *
     * @return UpdateUserCommand
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @param string $lastName
     *
     * @return UpdateUserCommand
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @param UserTitle|null $title
     *
     * @return UpdateUserCommand
     */
    public function setTitle(?UserTitle $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param \DateTimeInterface|null $birthday
     *
     * @return UpdateUserCommand
     */
    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * @param string $currencyCode
     *
     * @return UpdateUserCommand
     */
    public function setCurrencyCode(string $currencyCode): self
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    /** @return string|null */
    public function getExternalIdentifier(): ?string
    {
        return $this->externalIdentifier;
    }

    /**
     * @param string|null $externalIdentifier
     *
     * @return $this
     */
    public function setExternalIdentifier(?string $externalIdentifier): self
    {
        $this->externalIdentifier = $externalIdentifier;

        return $this;
    }

    /** @return bool|null */
    public function getIsProfessional(): ?bool
    {
        return $this->isProfessional;
    }

    /**
     * @param bool|null $isProfessional
     *
     * @return $this
     */
    public function setIsProfessional(?bool $isProfessional): self
    {
        $this->isProfessional = $isProfessional;

        return $this;
    }

    /** @return string|null */
    public function getIntraEuropeanCommunityVAT(): ?string
    {
        return $this->intraEuropeanCommunityVAT;
    }

    /**
     * @param string|null $intraEuropeanCommunityVAT
     *
     * @return $this
     */
    public function setIntraEuropeanCommunityVAT(?string $intraEuropeanCommunityVAT): self
    {
        $this->intraEuropeanCommunityVAT = $intraEuropeanCommunityVAT;

        return $this;
    }

    /** @return string|null */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * @param string|null $company
     *
     * @return $this
     */
    public function setCompany(?string $company): self
    {
        $this->company = $company;

        return $this;
    }

    /** @return string|null */
    public function getFunction(): ?string
    {
        return $this->function;
    }

    /**
     * @param string|null $function
     *
     * @return $this
     */
    public function setFunction(?string $function): self
    {
        $this->function = $function;

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
