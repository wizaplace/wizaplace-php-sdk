<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

final class Product extends ProductSummary
{
    /** @var null|ProductImage */
    private $mainImage;

    /** @var ProductImage[] */
    private $additionalImages;

    /** @var string */
    private $fullDescription;

    /** @var string */
    private $shortDescription;

    /** @var int[] */
    private $taxIds;

    /** @var ProductDeclination[] */
    private $declinations;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->fullDescription = $data['full_description'];
        $this->shortDescription = $data['short_description'];
        $this->taxIds = $data['tax_ids'];
        if (isset($data['main_pair']['image_name'])) {
            $this->mainImage = new ProductImage($data['main_pair']);
        }
        $this->additionalImages = array_map(static function (array $imageData): ProductImage {
            return new ProductImage($imageData);
        }, $data['image_pairs'] ?? []);
        $this->declinations = array_map(function (array $declinationData): ProductDeclination {
            return new ProductDeclination($declinationData);
        }, $data['inventory'] ?? []);
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

    public function getMainImage(): ?ProductImage
    {
        return $this->mainImage;
    }

    /**
     * @return ProductImage[]
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
}
