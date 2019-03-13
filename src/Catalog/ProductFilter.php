<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

/**
 * Class ProductFilter
 * @package Wizaplace\SDK\Catalog
 */
final class ProductFilter
{
    public const ID = 'id';
    public const CODE = 'code';
    public const SUPPLIER_REF = 'supplierRef';
    public const COMPANY_ID = 'companyId';

    /** @var int[]  */
    private $ids = [];

    /** @var string[]  */
    private $codes = [];

    /** @var string[]  */
    private $supplierRefs = [];

    /** @var null|int */
    private $companyId;

    public function getFilters(): array
    {
        $filters = [
            static::ID => $this->ids,
            static::CODE => $this->codes,
            static::SUPPLIER_REF => $this->supplierRefs,
            static::COMPANY_ID => $this->companyId,
        ];

        array_filter($filters, function ($item) {
            return empty($item) === false;
        });

        return $filters;
    }

    /** @return int[] */
    public function getIds(): array
    {
        return $this->ids;
    }

    public function setIds(array $id): self
    {
        $this->ids = $id;

        return $this;
    }

    /** @return string[] */
    public function getCodes(): array
    {
        return $this->codes;
    }

    public function setCodes(array $codes): self
    {
        $this->codes = $codes;

        return $this;
    }

    /** @return string[] */
    public function getSupplierRefs(): array
    {
        return $this->supplierRefs;
    }

    public function setSupplierRefs(array $supplierRefs): self
    {
        $this->supplierRefs = $supplierRefs;

        return $this;
    }

    public function getCompanyId(): ?int
    {
        return $this->companyId;
    }

    public function setCompanyId(int $companyId): self
    {
        $this->companyId = $companyId;

        return $this;
    }
}
