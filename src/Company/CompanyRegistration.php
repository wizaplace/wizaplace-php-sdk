<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Company;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

final class CompanyRegistration
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
     * @see \Wizaplace\Company\CompanyRegistration::addFile
     */
    private $files = [];

    public function __construct(string $name, string $email)
    {
        $this->name = $name;
        $this->email = $email;
    }


    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(?string $zipcode): void
    {
        $this->zipcode = $zipcode;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): void
    {
        $this->fax = $fax;
    }

    /**
     * @deprecated
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function getLegalStatus(): ?string
    {
        return $this->legalStatus;
    }

    public function setLegalStatus(?string $legalStatus): void
    {
        $this->legalStatus = $legalStatus;
    }

    public function getSiretNumber(): ?string
    {
        return $this->siretNumber;
    }

    public function setSiretNumber(?string $siretNumber): void
    {
        $this->siretNumber = $siretNumber;
    }

    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    public function setVatNumber(?string $vatNumber): void
    {
        $this->vatNumber = $vatNumber;
    }

    public function getCapital(): ?string
    {
        return $this->capital;
    }

    public function setCapital(?string $capital): void
    {
        $this->capital = $capital;
    }

    public function getRcs(): ?string
    {
        return $this->rcs;
    }

    public function setRcs(?string $rcs): void
    {
        $this->rcs = $rcs;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    public function addUploadedFile(string $name, UploadedFileInterface $file): void
    {
        $this->addFile(
            $name,
            $file->getStream(),
            $file->getClientFilename()
        );
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    private function addFile(string $name, StreamInterface $contents, string $filename): void
    {
        $this->files[$name] = [
            'name' => $name,
            'contents' => $contents,
            'filename' => $filename,
        ];
    }
}
