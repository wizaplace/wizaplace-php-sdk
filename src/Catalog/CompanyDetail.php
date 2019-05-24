<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog;

use Wizaplace\SDK\Image\Image;
use function theodorejb\polycast\to_int;
use function theodorejb\polycast\to_string;
use Wizaplace\SDK\Seo\Metadata;

/**
 * Class CompanyDetail
 * @package Wizaplace\SDK\Catalog
 */
final class CompanyDetail
{
    /** @var integer */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var string */
    private $address;

    /** @var string */
    private $phoneNumber;

    /** @var boolean */
    private $professional;

    /** @var string */
    private $slug;

    /** @var null|Image */
    private $image;

    /** @var null|Location */
    private $location;

    /** @var null|integer */
    private $averageRating;

    /** @var string */
    private $terms;

    /** @var CompanyAddress */
    private $fullAddress;

    /** @var array */
    private $extra;

    /** @var null|Metadata */
    private $metadata;

    public function __construct(array $data)
    {
        $this->id = to_int($data['id']);
        $this->name = to_string($data['name']);
        $this->description = to_string($data['description']);
        $this->address = to_string($data['address']);
        $this->phoneNumber = to_string($data['phoneNumber']);
        $this->professional = (bool) $data['professional'];
        $this->slug = to_string($data['slug']);
        $this->image = ($data['image'] !== null) ? new Image($data['image']) : null;
        if ($data['location'] !== null) {
            $this->location = new Location($data['location']['latitude'], $data['location']['longitude']);
        } else {
            $this->location = null;
        }
        $this->averageRating = $data['averageRating'];
        $this->terms = to_string($data['terms']);
        $this->fullAddress = new CompanyAddress($data['fullAddress']);
        $this->extra = (array) $data['extra'];
        $this->metadata = (array_key_exists('meta', $data) && count($data['meta']) > 0) ? new Metadata($data['meta']) : null;
    }

    public function getFullAddress(): CompanyAddress
    {
        return $this->fullAddress;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function isProfessional(): bool
    {
        return $this->professional;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function getAverageRating(): ?int
    {
        return $this->averageRating;
    }

    public function getTerms(): string
    {
        return $this->terms;
    }

    public function getExtra(): array
    {
        return $this->extra;
    }

    public function getMetadata(): ?Metadata
    {
        return $this->metadata;
    }
}
