<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Company;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

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
     * @internal
     */
    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->name = (string) $data['name'];
        $this->email = (string) $data['email'];
        $this->description = (string) $data['description'];
        $this->zipcode = (string) $data['zipcode'];
        $this->address = (string) $data['address'];
        $this->city = (string) $data['city'];
        $this->country = (string) $data['country'];
        $this->phoneNumber = (string) $data['phoneNumber'];
        $this->fax = (string) $data['fax'];
        $this->url = empty($data['url']) ? null : new Uri($data['url']);
        $this->legalStatus = (string) $data['legalStatus'];
        $this->siretNumber = (string) $data['siretNumber'];
        $this->vatNumber = (string) $data['vatNumber'];
        $this->capital = (string) $data['capital'];
        $this->rcs = (string) $data['rcs'];
        $this->slug = (string) $data['slug'];
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
}
