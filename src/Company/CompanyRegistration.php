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

    /**
     * @var array
     * @see \Wizaplace\SDK\Company\CompanyRegistration::addFile
     */
    private $files = [];

    public function __construct(
        string $name,
        string $email
    ) {
        $this->name = $name;
        $this->email = $email;
    }


    final public function getName(): string
    {
        return $this->name;
    }

    final public function setName(string $name): void
    {
        $this->name = $name;
    }

    final public function getEmail(): string
    {
        return $this->email;
    }

    final public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    final public function getDescription(): ?string
    {
        return $this->description;
    }

    final public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    final public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    final public function setZipcode(?string $zipcode): void
    {
        $this->zipcode = $zipcode;
    }

    final public function getAddress(): ?string
    {
        return $this->address;
    }

    final public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    final public function getCity(): ?string
    {
        return $this->city;
    }

    final public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    final public function getCountry(): ?string
    {
        return $this->country;
    }

    final public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    final public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    final public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    final public function getFax(): ?string
    {
        return $this->fax;
    }

    final public function setFax(?string $fax): void
    {
        $this->fax = $fax;
    }

    final public function getUrl(): ?string
    {
        return $this->url;
    }

    final public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    final public function getLegalStatus(): ?string
    {
        return $this->legalStatus;
    }

    final public function setLegalStatus(?string $legalStatus): void
    {
        $this->legalStatus = $legalStatus;
    }

    final public function getSiretNumber(): ?string
    {
        return $this->siretNumber;
    }

    final public function setSiretNumber(?string $siretNumber): void
    {
        $this->siretNumber = $siretNumber;
    }

    final public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    final public function setVatNumber(?string $vatNumber): void
    {
        $this->vatNumber = $vatNumber;
    }

    final public function getCapital(): ?string
    {
        return $this->capital;
    }

    final public function setCapital(?string $capital): void
    {
        $this->capital = $capital;
    }

    final public function getRcs(): ?string
    {
        return $this->rcs;
    }

    final public function setRcs(?string $rcs): void
    {
        $this->rcs = $rcs;
    }

    final public function getSlug(): ?string
    {
        return $this->slug;
    }

    final public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

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

    final private function addFile(string $name, StreamInterface $contents, string $filename): void
    {
        $this->files[$name] = [
            'name' => $name,
            'contents' => $contents,
            'filename' => $filename,
        ];
    }
}
