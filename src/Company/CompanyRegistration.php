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
 * Class CompanyRegistration
 * @package Wizaplace\SDK\Company
 */
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
     */
    final public function setName(string $name): void
    {
        $this->name = $name;
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
     */
    final public function setEmail(string $email): void
    {
        $this->email = $email;
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
     */
    final public function setDescription(?string $description): void
    {
        $this->description = $description;
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
     */
    final public function setZipcode(?string $zipcode): void
    {
        $this->zipcode = $zipcode;
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
     */
    final public function setAddress(?string $address): void
    {
        $this->address = $address;
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
     */
    final public function setCity(?string $city): void
    {
        $this->city = $city;
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
     */
    final public function setCountry(?string $country): void
    {
        $this->country = $country;
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
     */
    final public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
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
     */
    final public function setFax(?string $fax): void
    {
        $this->fax = $fax;
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
     */
    final public function setUrl(?string $url): void
    {
        $this->url = $url;
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
     */
    final public function setLegalStatus(?string $legalStatus): void
    {
        $this->legalStatus = $legalStatus;
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
     */
    final public function setSiretNumber(?string $siretNumber): void
    {
        $this->siretNumber = $siretNumber;
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
     */
    final public function setVatNumber(?string $vatNumber): void
    {
        $this->vatNumber = $vatNumber;
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
     */
    final public function setCapital(?string $capital): void
    {
        $this->capital = $capital;
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
     */
    final public function setRcs(?string $rcs): void
    {
        $this->rcs = $rcs;
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
     */
    final public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
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
     */
    final public function setExtra(array $extra): void
    {
        $this->extra = $extra;
    }

    /**
     * @param string                $name
     * @param UploadedFileInterface $file
     */
    final public function addUploadedFile(string $name, UploadedFileInterface $file): void
    {
        $this->addFile(
            $name,
            $file->getStream(),
            $file->getClientFilename()
        );
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
     */
    final public function setIban(?string $iban): void
    {
        $this->iban = $iban;
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
     */
    final public function setBic(?string $bic): void
    {
        $this->bic = $bic;
    }

    /**
     * @param string          $name
     * @param StreamInterface $contents
     * @param string          $filename
     */
    final private function addFile(string $name, StreamInterface $contents, string $filename): void
    {
        $this->files[$name] = [
            'name' => $name,
            'contents' => $contents,
            'filename' => $filename,
        ];
    }
}
