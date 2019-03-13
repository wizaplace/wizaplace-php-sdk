<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Company;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Class AbstractCompanyRegistration
 * @package Wizaplace\SDK\Company
 * @internal
 */
class AbstractCompanyRegistration
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $email;

    /** @var null|string */
    protected $description;

    /** @var null|string */
    protected $zipcode;

    /** @var null|string */
    protected $address;

    /** @var null|string */
    protected $city;

    /** @var null|string */
    protected $country;

    /** @var null|string */
    protected $phoneNumber;

    /** @var null|string */
    protected $fax;

    /** @var null|string */
    protected $url;

    /** @var null|string */
    protected $legalStatus;

    /** @var null|string */
    protected $siretNumber;

    /** @var null|string */
    protected $vatNumber;

    /** @var null|string */
    protected $capital;

    /** @var null|string */
    protected $rcs;

    /** @var null|string */
    protected $slug;

    /** @var null|string */
    protected $iban;

    /** @var null|string */
    protected $bic;

    /** @var array */
    protected $extra = [];

    /** @var bool */
    // phpcs:ignore
    protected $isC2C = false;

    /**
     * @var array
     * @see \Wizaplace\SDK\Company\CompanyRegistration::addFile
     */
    protected $files = [];

    /**
     * AbstractCompanyRegistration constructor.
     *
     * @param bool   $isC2C
     */
    public function __construct(bool $isC2C = false)
    {
        $this->isC2C = $isC2C;
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
     * @return AbstractCompanyRegistration
     */
    final public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    final public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return AbstractCompanyRegistration
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
     * @return AbstractCompanyRegistration
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
     * @return AbstractCompanyRegistration
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
     * @return AbstractCompanyRegistration
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
     * @return AbstractCompanyRegistration
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
     * @return AbstractCompanyRegistration
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
     * @return AbstractCompanyRegistration
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
     * @return AbstractCompanyRegistration
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
     * @return AbstractCompanyRegistration
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
     * @return AbstractCompanyRegistration
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
     * @return AbstractCompanyRegistration
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
     * @return AbstractCompanyRegistration
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
     * @return AbstractCompanyRegistration
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
     * @return AbstractCompanyRegistration
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
     * @return AbstractCompanyRegistration
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
     * @return AbstractCompanyRegistration
     */
    final public function setExtra(array $extra): self
    {
        $this->extra = $extra;

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
     * @param string                $name
     * @param UploadedFileInterface $file
     *
     * @return AbstractCompanyRegistration
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
     * @return string|null
     */
    final public function getIban(): ?string
    {
        return $this->iban;
    }

    /**
     * @param string|null $iban
     *
     * @return AbstractCompanyRegistration
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
     * @return AbstractCompanyRegistration
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
     * @param string          $name
     * @param StreamInterface $contents
     * @param string          $filename
     */
    final protected function addFile(string $name, StreamInterface $contents, string $filename): void
    {
        $this->files[$name] = [
            'name' => $name,
            'contents' => $contents,
            'filename' => $filename,
        ];
    }
}
