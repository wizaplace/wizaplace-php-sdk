<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Organisation;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Wizaplace\SDK\File\FileService;
use function theodorejb\polycast\to_string;

final class Organisation
{
    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var OrganisationAddress */
    private $address;

    /** @var OrganisationAddress */
    private $shippingAddress;

    /** @var string */
    private $legalInformationSiret;

    /** @var string */
    private $legalInformationVatNumber;

    /** @var string */
    private $legalInformationBusinessName;

    /** @var string */
    private $businessUnitCode;

    /** @var string */
    private $businessUnitName;

    /** @var string */
    private $status = 'pending';

    /** @var OrganisationAdministrator */
    private $administrator;

    /** @var array */
    private $files = [];

    public function __construct(array $data)
    {
        $this->id = isset($data['id']) ? to_string($data['id']) : null;
        $this->name = to_string($data['name']);
        $this->address = new OrganisationAddress($data['address']);
        $this->shippingAddress = new OrganisationAddress($data['shippingAddress']);
        $this->legalInformationSiret = to_string($data['siret']);
        $this->legalInformationVatNumber = to_string($data['vatNumber']);
        $this->legalInformationBusinessName = to_string($data['businessName']);
        $this->businessUnitCode = to_string($data['businessUnitCode']);
        $this->businessUnitName = to_string($data['businessUnitName']);
        $this->status = isset($data['status']) ? to_string($data['status']) : null;
        $this->administrator = isset($data['administrator']) ? new OrganisationAdministrator($data['administrator']) : null;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return OrganisationAddress
     */
    public function getAddress(): OrganisationAddress
    {
        return $this->address;
    }

    /**
     * @param OrganisationAddress $address
     */
    public function setAddress(OrganisationAddress $address): void
    {
        $this->address = $address;
    }

    /**
     * @return OrganisationAddress
     */
    public function getShippingAddress(): OrganisationAddress
    {
        return $this->shippingAddress;
    }

    /**
     * @param OrganisationAddress $shippingAddress
     */
    public function setShippingAddress(OrganisationAddress $shippingAddress): void
    {
        $this->shippingAddress = $shippingAddress;
    }

    /**
     * @return string
     */
    public function getLegalInformationSiret(): string
    {
        return $this->legalInformationSiret;
    }

    /**
     * @param string $legalInformationSiret
     */
    public function setLegalInformationSiret(string $legalInformationSiret): void
    {
        $this->legalInformationSiret = $legalInformationSiret;
    }

    /**
     * @return string
     */
    public function getLegalInformationVatNumber(): string
    {
        return $this->legalInformationVatNumber;
    }

    /**
     * @param string $legalInformationVatNumber
     */
    public function setLegalInformationVatNumber(string $legalInformationVatNumber): void
    {
        $this->legalInformationVatNumber = $legalInformationVatNumber;
    }

    /**
     * @return string
     */
    public function getLegalInformationBusinessName(): string
    {
        return $this->legalInformationBusinessName;
    }

    /**
     * @param string $legalInformationBusinessName
     */
    public function setLegalInformationBusinessName(string $legalInformationBusinessName): void
    {
        $this->legalInformationBusinessName = $legalInformationBusinessName;
    }

    /**
     * @return string
     */
    public function getBusinessUnitCode(): string
    {
        return $this->businessUnitCode;
    }

    /**
     * @param string $businessUnitCode
     */
    public function setBusinessUnitCode(string $businessUnitCode): void
    {
        $this->businessUnitCode = $businessUnitCode;
    }

    /**
     * @return string
     */
    public function getBusinessUnitName(): string
    {
        return $this->businessUnitName;
    }

    /**
     * @param string $businessUnitName
     */
    public function setBusinessUnitName(string $businessUnitName): void
    {
        $this->businessUnitName = $businessUnitName;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return OrganisationAdministrator
     */
    public function getAdministrator(): OrganisationAdministrator
    {
        return $this->administrator;
    }

    /**
     * @param OrganisationAdministrator $administrator
     */
    public function setAdministrator(OrganisationAdministrator $administrator): void
    {
        $this->administrator = $administrator;
    }

    public function addUploadedFile(string $name, UploadedFileInterface $file): void
    {
        $this->addFile(
            $name,
            $file->getStream(),
            $file->getClientFilename()
        );
    }

    /**
     * @return array
     * @see Organisation::addFile()
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    private function addFile(string $name, StreamInterface $contents, string $filename): void
    {
        $this->files[$name] = new FileService($name, $contents, $filename);
    }
}
