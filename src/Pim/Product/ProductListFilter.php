<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

use function theodorejb\polycast\to_string;
use Wizaplace\SDK\ArrayableInterface;

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

        if (count($this->ids) > 0) {
            $filters['pid'] = $this->ids;
        }

        if (count($this->productCodes) > 0) {
            $filters['pcode'] = $this->productCodes;

            if (isset($this->productCode)) {
                $filters['pcode'][] = $this->productCode;
            }
        }

        if (count($this->supplierReferences) > 0) {
            $filters['supplier_ref'] = $this->supplierReferences;
        }

        return $filters;
    }
}
