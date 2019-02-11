<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Company;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use function theodorejb\polycast\to_int;
use function theodorejb\polycast\to_string;

/**
 * Class Company
 * @package Wizaplace\SDK\Company
 */
final class Company
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $email;

    /** @var string */
    private $description;

    /** @var string */
    private $zipcode;

    /** @var string */
    private $address;

    /** @var string */
    private $city;

    /** @var string */
    private $country;

    /** @var string */
    private $phoneNumber;

    /** @var string */
    private $fax;

    /** @var null|UriInterface */
    private $url;

    /** @var string */
    private $legalStatus;

    /** @var string */
    private $siretNumber;

    /** @var string */
    private $vatNumber;

    /** @var string */
    private $capital;

    /** @var string */
    private $rcs;

    /** @var string */
    private $slug;

    /** @var null|string */
    private $iban;

    /** @var string */
    private $bic;

    /**
     * @var string
     */
    private $legalRepresentativeFirstName;

    /**
     * @var string
     */
    private $legalRepresentativeLastName;

    /**
     * @var array
     */
    private $extra;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = to_int($data['id']);
        $this->name = to_string($data['name']);
        $this->email = to_string($data['email']);
        $this->description = to_string($data['description']);
        $this->zipcode = to_string($data['zipcode']);
        $this->address = to_string($data['address']);
        $this->city = to_string($data['city']);
        $this->country = to_string($data['country']);
        $this->phoneNumber = to_string($data['phoneNumber']);
        $this->fax = to_string($data['fax']);
        $this->url = $data['url'] === '' ? null : new Uri($data['url']);
        $this->legalStatus = to_string($data['legalStatus']);
        $this->siretNumber = to_string($data['siretNumber']);
        $this->vatNumber = to_string($data['vatNumber']);
        $this->capital = to_string($data['capital']);
        $this->rcs = to_string($data['rcs']);
        $this->slug = to_string($data['slug']);
        $this->iban = (isset($data['iban']) && !is_null($data['iban'])) ? to_string($data['iban']) : "";
        $this->bic = (isset($data['bic']) && !is_null($data['bic'])) ? to_string($data['bic']) : "";
        $this->legalRepresentativeFirstName = to_string($data['legalRepresentativeFirstName']);
        $this->legalRepresentativeLastName = to_string($data['legalRepresentativeLastName']);
        $this->extra = (array) $data['extra'];
    }

    /**
     * @return int
     */
    public function getId(): int
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
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getZipcode(): string
    {
        return $this->zipcode;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * @return string
     */
    public function getFax(): string
    {
        return $this->fax;
    }

    /**
     * @return UriInterface|null
     */
    public function getUrl(): ?UriInterface
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getLegalStatus(): string
    {
        return $this->legalStatus;
    }

    /**
     * @return string
     */
    public function getSiretNumber(): string
    {
        return $this->siretNumber;
    }

    /**
     * @return string
     */
    public function getVatNumber(): string
    {
        return $this->vatNumber;
    }

    /**
     * @return string
     */
    public function getCapital(): string
    {
        return $this->capital;
    }

    /**
     * @return string
     */
    public function getRcs(): string
    {
        return $this->rcs;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getLegalRepresentativeFirstName(): string
    {
        return $this->legalRepresentativeFirstName;
    }

    /**
     * @return string
     */
    public function getLegalRepresentativeLastName(): string
    {
        return $this->legalRepresentativeLastName;
    }

    /**
     * @return array
     */
    public function getExtra(): array
    {
        return $this->extra;
    }

    /**
     * @return string|null
     */
    public function getIban(): ?string
    {
        return $this->iban;
    }

    /**
     * @return string
     */
    public function getBic(): string
    {
        return $this->bic;
    }
}
