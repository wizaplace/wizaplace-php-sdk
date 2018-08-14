<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Organisation;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
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
    private $shipping_address;

    /** @var string */
    private $legal_information_siret;

    /** @var string */
    private $legal_information_vat_number;

    /** @var string */
    private $legal_information_business_name;

    /** @var string */
    private $business_unit_code;

    /** @var string */
    private $business_unit_name;

    /** @var string */
    private $status = 'pending';

    /** @var array */
    private $administrator;

    /** @var array */
    private $files = [];

    public function __construct(array $data)
    {
        $this->id = to_string($data['id']);
        $this->name = to_string($data['name']);
        $this->address = $data['address'];
        $this->shipping_address = $data['shippingAddress'];
        $this->legal_information_siret = to_string($data['siret']);
        $this->legal_information_vat_number = to_string($data['vatNumber']);
        $this->legal_information_business_name = to_string($data['businessName']);
        $this->business_unit_code = to_string($data['businessUnitCode']);
        $this->business_unit_name = to_string($data['businessUnitName']);
        $this->status = to_string($data['status']);
        $this->administrator = isset($data['administrator']) ? $data['administrator'] : null;
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
        return $this->shipping_address;
    }

    /**
     * @param OrganisationAddress $shipping_address
     */
    public function setShippingAddress(OrganisationAddress $shipping_address): void
    {
        $this->shipping_address = $shipping_address;
    }

    /**
     * @return string
     */
    public function getLegalInformationSiret(): string
    {
        return $this->legal_information_siret;
    }

    /**
     * @param string $legal_information_siret
     */
    public function setLegalInformationSiret(string $legal_information_siret): void
    {
        $this->legal_information_siret = $legal_information_siret;
    }

    /**
     * @return string
     */
    public function getLegalInformationVatNumber(): string
    {
        return $this->legal_information_vat_number;
    }

    /**
     * @param string $legal_information_vat_number
     */
    public function setLegalInformationVatNumber(string $legal_information_vat_number): void
    {
        $this->legal_information_vat_number = $legal_information_vat_number;
    }

    /**
     * @return string
     */
    public function getLegalInformationBusinessName(): string
    {
        return $this->legal_information_business_name;
    }

    /**
     * @param string $legal_information_business_name
     */
    public function setLegalInformationBusinessName(string $legal_information_business_name): void
    {
        $this->legal_information_business_name = $legal_information_business_name;
    }

    /**
     * @return string
     */
    public function getBusinessUnitCode(): string
    {
        return $this->business_unit_code;
    }

    /**
     * @param string $business_unit_code
     */
    public function setBusinessUnitCode(string $business_unit_code): void
    {
        $this->business_unit_code = $business_unit_code;
    }

    /**
     * @return string
     */
    public function getBusinessUnitName(): string
    {
        return $this->business_unit_name;
    }

    /**
     * @param string $business_unit_name
     */
    public function setBusinessUnitName(string $business_unit_name): void
    {
        $this->business_unit_name = $business_unit_name;
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
     * @return array
     */
    public function getAdministrator(): array
    {
        return $this->administrator;
    }

    /**
     * @param array $administrator
     */
    public function setAdministrator(array $administrator): void
    {
        $this->administrator = $administrator;
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
     * @see Organisation::addFile()
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
