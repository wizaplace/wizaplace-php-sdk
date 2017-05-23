<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Basket;

use Wizaplace\Catalog\Company;

class BasketCompanyGroup
{
    /** @var string */
    private $id;

    /** @var Company */
    private $company;

    /** @var BasketShippingGroup[] */
    private $shippingGroups;

    public function __construct(array $data)
    {
        //$this->id = $data['id'];
        $this->company = new Company($data['company']);

        $this->shippingGroups = array_map(
            function ($shippingGroup) {
                return new BasketShippingGroup($shippingGroup);
            },
            $data['shippingGroups']
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    /**
     * @return BasketShippingGroup[]
     */
    public function getShippingGroups(): array
    {
        return $this->shippingGroups;
    }
}
