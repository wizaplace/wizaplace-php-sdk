<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

/**
 * Class ProductOffer
 * @package Wizaplace\SDK\Catalog
 */
class ProductOffer
{
    /** @var int */
    private $productId;
    /** @var int */
    private $companyId;
    /** @var float */
    private $price;
    /** @var array */
    private $divisions;

    /**
     * ProductOffer constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->productId = (int) $data['productId'];
        $this->companyId = (int) $data['companyId'];
        $this->price     = (float) $data['price'];
        $this->divisions = is_array($data['divisions']) ? array_filter($data['divisions']) : [];
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @return int
     */
    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return array
     */
    public function getDivisions(): array
    {
        return $this->divisions;
    }
}
