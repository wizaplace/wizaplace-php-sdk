<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use function theodorejb\polycast\to_float;

/**
 * Class ProductSummary
 * @package Wizaplace\SDK\Pim\Product
 */
class ProductSummary
{
    /** @var int */
    private $id;

    /** @var string */
    private $code;

    /** @var string */
    private $supplierReference;

    /** @var string */
    private $name;

    /** @var ProductStatus */
    private $status;

    /** @var ProductApprovalStatus */
    private $approvalStatus;

    /** @var \DateTimeImmutable */
    private $createdAt;

    /** @var \DateTimeImmutable */
    private $lastUpdateAt;

    /** @var int */
    private $companyId;

    /** @var int */
    private $mainCategoryId;

    /** @var float */
    private $greenTax;

    /** @var bool */
    private $isBrandNew;

    /** @var null|ProductGeolocation */
    private $geolocation;

    /** @var array */
    private $freeAttributes;

    /** @var bool */
    private $hasFreeShipping;

    /** @var float */
    private $weight;

    /** @var bool */
    private $isDownloadable;

    /** @var null|UriInterface */
    private $affiliateLink;

    /** @var ProductAttachment[] */
    private $attachments;

    /** @var array */
    private $divisions;

    /** @var ProductInventory[] */
    private $inventory;

    /**
     * @internal
     *
     * @param array $data
     *
     * @throws \Exception
     */
    public function __construct(array $data)
    {
        $this->id = $data['product_id'];
        $this->code = $data['product_code'];
        $this->supplierReference = $data['supplier_ref'];
        $this->name = $data['product'];
        $this->createdAt = new \DateTimeImmutable('@'.$data['timestamp']);
        $this->lastUpdateAt = new \DateTimeImmutable('@'.$data['updated_timestamp']);
        $this->companyId = $data['company_id'];
        $this->mainCategoryId = $data['main_category'];
        $this->weight = to_float($data['weight']);
        if (isset($data['affiliate_link']) && $data['affiliate_link'] !== '') {
            $this->affiliateLink = new Uri($data['affiliate_link']);
        }
        $this->isDownloadable = isset($data['is_edp']) && $data['is_edp'] === 'Y';
        $this->hasFreeShipping = $data['free_shipping'] === 'Y';
        $this->status = new ProductStatus($data['status']);
        $this->approvalStatus = new ProductApprovalStatus($data['approved']);
        $this->greenTax = to_float($data['green_tax']);
        if (isset($data['geolocation']['latitude']) || isset($data['geolocation']['longitude'])) { // we have to check it, because the API sometimes returns an object with no value instead of null
            $this->geolocation = new ProductGeolocation($data['geolocation']);
        }
        $this->isBrandNew = $data['condition'] === 'N';
        $this->freeAttributes = $data['free_features'];
        $this->attachments = array_map(static function (array $attachmentData): ProductAttachment {
            return new ProductAttachment($attachmentData);
        }, $data['attachments'] ?? []);
        $this->divisions = $data['divisions'] ?? [];
        $this->inventory = array_map(function (array $inventoryData): ProductInventory {
            return new ProductInventory($inventoryData);
        }, $data['inventory'] ?? []);
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
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getSupplierReference(): string
    {
        return $this->supplierReference;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getLastUpdateAt(): \DateTimeInterface
    {
        return $this->lastUpdateAt;
    }

    /**
     * @return int
     */
    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    /**
     * @return int
     */
    public function getMainCategoryId(): int
    {
        return $this->mainCategoryId;
    }

    /**
     * @return bool
     */
    public function hasFreeShipping(): bool
    {
        return $this->hasFreeShipping;
    }

    /**
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }

    /**
     * @return bool
     */
    public function isDownloadable(): bool
    {
        return $this->isDownloadable;
    }

    /**
     * @return UriInterface|null
     */
    public function getAffiliateLink(): ?UriInterface
    {
        return $this->affiliateLink;
    }

    /**
     * @return ProductStatus
     */
    public function getStatus(): ProductStatus
    {
        return $this->status;
    }

    /**
     * @return ProductApprovalStatus
     */
    public function getApprovalStatus(): ProductApprovalStatus
    {
        return $this->approvalStatus;
    }

    /**
     * @return float
     */
    public function getGreenTax(): float
    {
        return $this->greenTax;
    }

    /**
     * @return ProductGeolocation|null
     */
    public function getGeolocation(): ?ProductGeolocation
    {
        return $this->geolocation;
    }

    /**
     * @return bool
     */
    public function isBrandNew(): bool
    {
        return $this->isBrandNew;
    }

    /**
     * @return array a map of (string) key to (mixed) value
     */
    public function getFreeAttributes(): array
    {
        return $this->freeAttributes;
    }

    /**
     * @return ProductAttachment[]
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * @return array
     */
    public function getDivisions(): array
    {
        return $this->divisions;
    }

    public function getInventory(): array
    {
        return $this->inventory;
    }
}
