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
        $this->legalRepresentativeFirstName = to_string($data['legalRepresentativeFirstName']);
        $this->legalRepresentativeLastName = to_string($data['legalRepresentativeLastName']);
        $this->extra = (array) $data['extra'];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getZipcode(): string
    {
        return $this->zipcode;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function getFax(): string
    {
        return $this->fax;
    }

    public function getUrl(): ?UriInterface
    {
        return $this->url;
    }

    public function getLegalStatus(): string
    {
        return $this->legalStatus;
    }

    public function getSiretNumber(): string
    {
        return $this->siretNumber;
    }

    public function getVatNumber(): string
    {
        return $this->vatNumber;
    }

    public function getCapital(): string
    {
        return $this->capital;
    }

    public function getRcs(): string
    {
        return $this->rcs;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getLegalRepresentativeFirstName(): string
    {
        return $this->legalRepresentativeFirstName;
    }

    public function getLegalRepresentativeLastName(): string
    {
        return $this->legalRepresentativeLastName;
    }

    public function getExtra(): array
    {
        return $this->extra;
    }
}
