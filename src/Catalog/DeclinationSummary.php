<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use Wizaplace\SDK\Image\Image;
use function theodorejb\polycast\to_string;

/**
 * Class DeclinationSummary
 * @package Wizaplace\SDK\Catalog
 */
final class DeclinationSummary
{
    /** @var DeclinationId */
    private $id;

    /** @var string */
    private $productId;

    /** @var string */
    private $name;

    /** @var string */
    private $slug;

    /** @var string */
    private $code;

    /** @var float|null  */
    private $crossedOutPrice;

    /** @var float */
    private $priceWithTaxes;

    /** @var float */
    private $priceWithoutVat;

    /** @var float */
    private $vat;

    /** @var int */
    private $amount;

    /** @var null|UriInterface */
    private $affiliateLink;

    /** @var DeclinationOption[] */
    private $options;

    /** @var Image|null */
    private $mainImage;

    /** @var CompanySummary */
    private $company;

    /** @var ProductCategory[] */
    private $categoryPath;

    /** @var bool */
    private $isAvailable;

    /** @var string */
    private $shortDescription;

    /** @var bool */
    private $infiniteStock;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = new DeclinationId($data['id']);
        $this->productId = to_string($data['productId']);
        $this->name = $data['name'];
        $this->code = $data['code'];
        $this->priceWithTaxes = $data['prices']['priceWithTaxes'];
        $this->priceWithoutVat = $data['prices']['priceWithoutVat'];
        $this->vat = $data['prices']['vat'];
        $this->crossedOutPrice = $data['crossedOutPrice'] ?? null;
        $this->amount = $data['amount'];
        $this->affiliateLink = $data['affiliateLink'] === '' ? null : new Uri($data['affiliateLink']);
        $this->options = array_map(static function (array $data): DeclinationOption {
            return new DeclinationOption($data);
        }, $data['options']);
        $this->mainImage = isset($data['mainImage']) ? new Image($data['mainImage']) : null;
        $this->company = new CompanySummary($data['company']);
        $this->slug = $data['slug'];
        $this->categoryPath = array_map(static function (array $data): ProductCategory {
            return new ProductCategory($data);
        }, $data['categoryPath']);
        $this->isAvailable = $data['isAvailable'];
        $this->shortDescription = $data['shortDescription'];
        $this->infiniteStock = $data['infiniteStock'];
    }

    /**
     * @return DeclinationId
     */
    public function getId(): DeclinationId
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
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
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return float
     */
    public function getPriceWithTaxes(): float
    {
        return $this->priceWithTaxes;
    }

    /**
     * @return float
     */
    public function getPriceWithoutVat(): float
    {
        return $this->priceWithoutVat;
    }

    /**
     * @return float
     */
    public function getVat(): float
    {
        return $this->vat;
    }

    /**
     * @return float|null
     */
    public function getCrossedOutPrice(): ?float
    {
        return $this->crossedOutPrice;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @return UriInterface|null
     */
    public function getAffiliateLink(): ?UriInterface
    {
        return $this->affiliateLink;
    }

    /**
     * @return DeclinationOption[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return Image|null
     */
    public function getMainImage(): ?Image
    {
        return $this->mainImage;
    }

    /**
     * @return CompanySummary
     */
    public function getCompany(): CompanySummary
    {
        return $this->company;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return ProductCategory[]
     */
    public function getCategoryPath(): array
    {
        return $this->categoryPath;
    }

    /**
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    /**
     * @return string
     */
    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    /**
     * @return bool
     */
    public function hasInfiniteStock(): bool
    {
        return $this->infiniteStock;
    }
}
