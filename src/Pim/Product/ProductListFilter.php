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

        return $filters;
    }
}
