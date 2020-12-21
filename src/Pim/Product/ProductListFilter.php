<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

use Wizaplace\SDK\ArrayableInterface;

use function theodorejb\polycast\to_string;

/**
 * Class ProductListFilter
 * @package Wizaplace\SDK\Pim\Product
 */
final class ProductListFilter implements ArrayableInterface
{
    /** @var null|string */
    private $productCode;

    /** @var int[] */
    private $categoryIds = [];

    /** @var bool */
    private $includeSubCategories = false;

    /** @var  null|ProductStatus */
    private $status;

    /** @var int[] */
    private $ids = [];

    /** @var string[]  */
    private $supplierReferences = [];

    /** @var string[]  */
    private $productCodes = [];

    /** @var null|ProductApprovalStatus */
    private $approvalStatus;

    /** @var null|string */
    private $updatedBefore;

    /** @var null|string */
    private $updatedAfter;

    /** @var int[] */
    private $companyIds = [];

    /**
     * @param string $productCode
     *
     * @return ProductListFilter
     */
    public function byProductCode(string $productCode): self
    {
        $this->productCode = $productCode;

        return $this;
    }

    /**
     * @param int[] $categoryIds
     * @param bool $includeSubCategories
     * @return ProductListFilter
     */
    public function byCategoryIds(array $categoryIds, bool $includeSubCategories): self
    {
        $this->categoryIds = $categoryIds;
        $this->includeSubCategories = $includeSubCategories;

        return $this;
    }

    /**
     * @param ProductStatus $status
     *
     * @return ProductListFilter
     */
    public function byStatus(ProductStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param int[] $ids
     * @return ProductListFilter
     */
    public function byIds(array $ids): self
    {
        $this->ids = $ids;

        return $this;
    }

    /**
     * @param string[] $supplierReferences
     * @return ProductListFilter
     */
    public function bySupplierReferences(array $supplierReferences): self
    {
        $this->supplierReferences = $supplierReferences;

        return $this;
    }

    /**
     * @param string[] $productCodes
     * @return ProductListFilter
     */
    public function byProductCodes(array $productCodes): self
    {
        $this->productCodes = $productCodes;

        return $this;
    }

    public function byApprovalStatus(ProductApprovalStatus $approvalStatus): self
    {
        $this->approvalStatus = $approvalStatus;

        return $this;
    }

    public function byUpdatedBefore(string $updatedBefore): self
    {
        $this->updatedBefore = $updatedBefore;

        return $this;
    }

    public function byUpdatedAfter(string $updatedAfter): self
    {
        $this->updatedAfter = $updatedAfter;

        return $this;
    }

    /** @param int[] $companyIds */
    public function byCompanyIds(array $companyIds): self
    {
        $this->companyIds = $companyIds;

        return $this;
    }

    /**
     * @internal
     */
    public function toArray(): array
    {
        $filters = [];
        if (isset($this->status)) {
            $filters['status'] = to_string($this->status);
        }
        if (isset($this->productCode)) {
            $filters['pcode'] = $this->productCode;
        }
        if (!empty($this->categoryIds)) {
            $filters['cid'] = $this->categoryIds;
            $filters['subcats'] = $this->includeSubCategories ? 'Y' : 'N';
        }

        if (\count($this->ids) > 0) {
            $filters['pid'] = $this->ids;
        }

        if (\count($this->productCodes) > 0) {
            $filters['pcode'] = $this->productCodes;

            if (isset($this->productCode)) {
                $filters['pcode'][] = $this->productCode;
            }
        }

        if ($this->approvalStatus instanceof ProductApprovalStatus) {
            // Name is changed here, because API don't have the same field name.
            $filters['approved'] = to_string($this->approvalStatus);
        }

        if (\is_string($this->updatedAfter)) {
            $filters['updatedAfter'] = $this->updatedAfter;
        }

        if (\is_string($this->updatedBefore)) {
            $filters['updatedBefore'] = $this->updatedBefore;
        }

        if (\count($this->supplierReferences) > 0) {
            $filters['supplier_ref'] = $this->supplierReferences;
        }

        if (\count($this->companyIds) > 0) {
            $filters['company_ids'] = $this->companyIds;
        }

        return $filters;
    }
}
