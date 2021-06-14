<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

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

    /** @var string|null */
    private $phone;

    /** @var string */
    private $externalIdentifier;

    /** @var bool|null */
    private $isProfessional;

    /** @var string|null */
    private $intraEuropeanCommunityVAT;

    /** @var string|null */
    private $company;

    /** @var string|null */
    private $jobTitle;

    /** @var string|null */
    private $comment;

    /** @var string|null */
    private $legalIdentifier;

    /** @var string|null */
    private $loyaltyIdentifier;

    /** @var string|null */
    private $lang;

    /** @var Nationality[]|null */
    private $nationalities;

    /** @var string[]|int[]|null */
    private $extra;

    /** @return string|null */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     *
     * @return self
     */
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
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
     *
     * @return self
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
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
     *
     * @return self
     */
    public function setTitle(?UserTitle $title): self
    {
        $this->title = $title;

        return $this;
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
     *
     * @return self
     */
    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
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
     *
     * @return self
     */
    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
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
     *
     * @return self
     */
    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    /** @return UpdateUserAddressCommand|null */
    public function getBilling(): ?UpdateUserAddressCommand
    {
        return $this->billing;
    }

    /**
     * @param UpdateUserAddressCommand|null $billing
     *
     * @return self
     */
    public function setBilling(?UpdateUserAddressCommand $billing): self
    {
        $this->billing = $billing;

        return $this;
    }

    /** @return UpdateUserAddressCommand|null */
    public function getShipping(): ?UpdateUserAddressCommand
    {
        return $this->shipping;
    }

    /**
     * @param UpdateUserAddressCommand|null $shipping
     *
     * @return self
     */
    public function setShipping(?UpdateUserAddressCommand $shipping): self
    {
        $this->shipping = $shipping;

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
    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    /**
     * @param string|null $jobTitle
     *
     * @return $this
     */
    public function setJobTitle(?string $jobTitle): self
    {
        $this->jobTitle = $jobTitle;

        return $this;
    }

    /** @return string|null */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     *
     * @return $this
     */
    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /** @return string|null */
    public function getLegalIdentifier(): ?string
    {
        return $this->legalIdentifier;
    }

    /**
     * @param string|null $legalIdentifier
     *
     * @return $this
     */
    public function setLegalIdentifier(?string $legalIdentifier): self
    {
        $this->legalIdentifier = $legalIdentifier;

        return $this;
    }

    /** @return string|null */
    public function getLoyaltyIdentifier(): ?string
    {
        return $this->loyaltyIdentifier;
    }

    /**
     * @param string|null $loyaltyIdentifier
     *
     * @return $this
     */
    public function setLoyaltyIdentifier(?string $loyaltyIdentifier): self
    {
        $this->loyaltyIdentifier = $loyaltyIdentifier;

        return $this;
    }

    /** @param string|null $lang */
    public function setLanguage(?string $lang): self
    {
        $this->lang = $lang;

        return $this;
    }

    /** @return null|string */
    public function getLanguage(): ?string
    {
        return $this->lang;
    }

    /** @return Nationality[]|null */
    public function getNationalities(): ?array
    {
        return $this->nationalities;
    }

    /** @params Nationality[] $nationalities */
    public function setNationalities(array $nationalities): self
    {
        foreach ($nationalities as $nationality) {
            $this->addNationality($nationality);
        }

        return $this;
    }

    public function addNationality(Nationality $nationality): self
    {
        if (\in_array($nationality->getCountryCodeA3(), $this->getCodesA3FromNationalities()) === false) {
            $this->nationalities[] = $nationality;
        }

        return $this;
    }

    /** @return string[] */
    public function getCodesA3FromNationalities(): array
    {
        if ($this->getNationalities() === null) {
            return [];
        }

        return  \array_map(
            function ($nationality) {
                return $nationality->getCountryCodeA3();
            },
            $this->getNationalities()
        );
    }

    /** @params string[] $codesA3 */
    public function setNationalitiesFromCodesA3(array $codesA3): self
    {
        $this->nationalities = \array_map(
            function ($codeA3) {
                return new Nationality($codeA3);
            },
            $codesA3
        );

        return $this;
    }

    /** @return string[]|int[]|null */
    public function getExtra(): ?array
    {
        return $this->extra;
    }

    /** @params string[]|int[]|null $extra */
    public function setExtra(array $extra): self
    {
        $this->extra = $extra;

        return $this;
    }
}
