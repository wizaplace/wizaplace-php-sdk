<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Company;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

class CompanyRegistration
{
    /** @var string */
    private $name;

    /** @var string */
    private $email;

    /** @var null|string */
    private $description;

    /** @var null|string */
    private $zipcode;

    /** @var null|string */
    private $address;

    /** @var null|string */
    private $city;

    /** @var null|string */
    private $country;

    /** @var null|string */
    private $phoneNumber;

    /** @var null|string */
    private $fax;

    /** @var null|string */
    private $url;

    /** @var null|string */
    private $legalStatus;

    /** @var null|string */
    private $siretNumber;

    /** @var null|string */
    private $vatNumber;

    /** @var null|string */
    private $capital;

    /** @var null|string */
    private $rcs;

    /** @var null|string */
    private $slug;

    /** @var null|string */
    private $iban;

    /** @var null|string */
    private $bic;

    /** @var array */
    private $extra = [];

    /** @var bool */
    // phpcs:ignore
    private $isC2C = false;

    /**
     * @var array
     * @see \Wizaplace\SDK\Company\CompanyRegistration::addFile
     */
    private $files = [];

    /**
     * CompanyRegistration constructor.
     *
     * @param string $name
     * @param string $email
     */
    public function __construct(string $name, string $email)
    {
        $this->name = $name;
        $this->email = $email;
    }

    /**
     * @return string
     */
    final public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return CompanyRegistration
     */
    final public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    final public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return CompanyRegistration
     */
    final public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    final public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return CompanyRegistration
     */
    final public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    final public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    /**
     * @param string|null $zipcode
     *
     * @return CompanyRegistration
     */
    final public function setZipcode(?string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * @return string|null
     */
    final public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     *
     * @return CompanyRegistration
     */
    final public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string|null
     */
    final public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     *
     * @return CompanyRegistration
     */
    final public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string|null
     */
    final public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param string|null $country
     *
     * @return CompanyRegistration
     */
    final public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return string|null
     */
    final public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string|null $phoneNumber
     *
     * @return CompanyRegistration
     */
    final public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    final public function getFax(): ?string
    {
        return $this->fax;
    }

    /**
     * @param string|null $fax
     *
     * @return CompanyRegistration
     */
    final public function setFax(?string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * @return string|null
     */
    final public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     *
     * @return CompanyRegistration
     */
    final public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string|null
     */
    final public function getLegalStatus(): ?string
    {
        return $this->legalStatus;
    }

    /**
     * @param string|null $legalStatus
     *
     * @return CompanyRegistration
     */
    final public function setLegalStatus(?string $legalStatus): self
    {
        $this->legalStatus = $legalStatus;

        return $this;
    }

    /**
     * @return string|null
     */
    final public function getSiretNumber(): ?string
    {
        return $this->siretNumber;
    }

    /**
     * @param string|null $siretNumber
     *
     * @return CompanyRegistration
     */
    final public function setSiretNumber(?string $siretNumber): self
    {
        $this->siretNumber = $siretNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    final public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    /**
     * @param string|null $vatNumber
     *
     * @return CompanyRegistration
     */
    final public function setVatNumber(?string $vatNumber): self
    {
        $this->vatNumber = $vatNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    final public function getCapital(): ?string
    {
        return $this->capital;
    }

    /**
     * @param string|null $capital
     *
     * @return CompanyRegistration
     */
    final public function setCapital(?string $capital): self
    {
        $this->capital = $capital;

        return $this;
    }

    /**
     * @return string|null
     */
    final public function getRcs(): ?string
    {
        return $this->rcs;
    }

    /**
     * @param string|null $rcs
     *
     * @return CompanyRegistration
     */
    final public function setRcs(?string $rcs): self
    {
        $this->rcs = $rcs;

        return $this;
    }

    /**
     * @return string|null
     */
    final public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string|null $slug
     *
     * @return CompanyRegistration
     */
    final public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return array
     */
    final public function getExtra(): array
    {
        return $this->extra;
    }

    /**
     * @param array $extra
     *
     * @return CompanyRegistration
     */
    final public function setExtra(array $extra): self
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * @param string                $name
     * @param UploadedFileInterface $file
     *
     * @return CompanyRegistration
     */
    final public function addUploadedFile(string $name, UploadedFileInterface $file): self
    {
        $this->addFile(
            $name,
            $file->getStream(),
            $file->getClientFilename()
        );

        return $this;
    }

    /**
     * @return array
     * @see \Wizaplace\SDK\Company\CompanyRegistration::addFile
     */
    final public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @return string|null
     */
    final public function getIban(): ?string
    {
        return $this->iban;
    }

    /**
     * @param string|null $iban
     *
     * @return CompanyRegistration
     */
    final public function setIban(?string $iban): self
    {
        $this->iban = $iban;

        return $this;
    }

    /**
     * @return string|null
     */
    final public function getBic(): ?string
    {
        return $this->bic;
    }

    /**
     * @param string|null $bic
     *
     * @return CompanyRegistration
     */
    final public function setBic(?string $bic): self
    {
        $this->bic = $bic;

        return $this;
    }

    /**
     * @return bool
     */
    final public function isC2C(): bool
    {
        return $this->isC2C;
    }

    /**
     * @param bool $isC2C
     *
     * @return CompanyRegistration
     */
    final public function setIsC2C(bool $isC2C): self
    {
        $this->isC2C = $isC2C;

        return $this;
    }

    final private function addFile(string $name, StreamInterface $contents, string $filename): void
    {
        $this->files[$name] = [
            'name' => $name,
            'contents' => $contents,
            'filename' => $filename,
        ];
    }
}
