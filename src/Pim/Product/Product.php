<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

final class Product extends ProductSummary
{
    /** @var UriInterface */
    private $mainImage;

    /** @var UriInterface[] */
    private $additionalImages;

    /** @var string */
    private $fullDescription;

    /** @var string */
    private $shortDescription;

    /** @var int[] */
    private $taxIds;

    /** @var ProductDeclination[] */
    private $declinations;

    /** @var \DateTimeImmutable */
    private $availibilityDate;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->fullDescription = $data['full_description'];
        $this->shortDescription = $data['short_description'];
        $this->taxIds = $data['tax_ids'];
        if (isset($data['main_pair']['detailed']['image_path'])) {
            $this->mainImage = self::unserializeImage($data['main_pair']);
        }
        $this->additionalImages = array_map([self::class, 'unserializeImage'], $data['image_pairs'] ?? []);
        $this->declinations = array_map(static function (array $declinationData): ProductDeclination {
            return new ProductDeclination($declinationData);
        }, $data['inventory'] ?? []);
        $this->availibilityDate = new \DateTimeImmutable('@'.$data['avail_since']);
    }

    public function getFullDescription(): string
    {
        return $this->fullDescription;
    }

    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    /**
     * @return int[]
     */
    public function getTaxIds(): array
    {
        return $this->taxIds;
    }

    public function getMainImage(): ?UriInterface
    {
        return $this->mainImage;
    }

    /**
     * @return UriInterface[]
     */
    public function getAdditionalImages(): array
    {
        return $this->additionalImages;
    }

    /**
     * @return ProductDeclination[]
     */
    public function getDeclinations(): array
    {
        return $this->declinations;
    }

    public function getAvailibilityDate(): \DateTimeImmutable
    {
        return $this->availibilityDate;
    }

    private static function unserializeImage(array $imageData): UriInterface
    {
        return new Uri($imageData['detailed']['image_path']);
    }
}
