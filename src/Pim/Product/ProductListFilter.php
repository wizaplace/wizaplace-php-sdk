<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

use function theodorejb\polycast\to_string;

final class ProductListFilter
{
    /** @var null|string */
    private $productCode;

    /** @var int[] */
    private $categoryIds = [];

    /** @var bool */
    private $includeSubCategories = false;

    /** @var  null|ProductStatus */
    private $status;

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

    public function byStatus(ProductStatus $status): self
    {
        $this->status = $status;
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
