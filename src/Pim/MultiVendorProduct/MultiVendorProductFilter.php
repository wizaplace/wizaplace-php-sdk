<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Pim\MultiVendorProduct;

use Wizaplace\SDK\ArrayableInterface;

final class MultiVendorProductFilter implements ArrayableInterface
{
    /** @var string[]  */
    private $ids = [];

    /** @var string[]  */
    private $codes = [];

    /** @var string[]  */
    private $supplierReferences = [];

    /** @var string[]  */
    private $categoryId = [];

    /** @var string|null  */
    private $updatedAfter;

    /** @var string|null  */
    private $updatedBefore;

    /**
     * @param string[] $id
     * @return MultiVendorProductFilter
     */
    public function setIds(array $id): self
    {
        $this->ids = $id;

        return $this;
    }

    /**
     * @param string[] $code
     * @return MultiVendorProductFilter
     */
    public function setCodes(array $code): self
    {
        $this->codes = $code;

        return $this;
    }

    public function setCategoryId(array $categoryId): self
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * @param string[] $supplierReference
     * @return MultiVendorProductFilter
     */
    public function setSupplierReferences(array $supplierReference): self
    {
        $this->supplierReferences = $supplierReference;

        return $this;
    }

    public function setUpdatedAfter(?string $updatedAfter): self
    {
        $this->updatedAfter = $updatedAfter;

        return $this;
    }

    public function setUpdatedBefore(?string $updatedBefore): self
    {
        $this->updatedBefore = $updatedBefore;

        return $this;
    }

    public function toArray(): array
    {
        return array_filter(
            [
                'id' => $this->ids,
                'code' => $this->codes,
                'supplierReference' => $this->supplierReferences,
                'categoryId' => $this->categoryId,
                'updatedBefore' => $this->updatedBefore,
                'updatedAfter' => $this->updatedAfter,
            ]
        );
    }
}
