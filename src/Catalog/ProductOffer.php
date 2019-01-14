<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

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

    public function __construct(array $data)
    {
        $this->productId = (int) $data['productId'];
        $this->companyId = (int) $data['companyId'];
        $this->price     = (float) $data['price'];
        $this->divisions = is_array($data['divisions']) ? array_filter($data['divisions']) : [];
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getDivisions(): array
    {
        return $this->divisions;
    }
}
