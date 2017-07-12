<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Basket;

class BasketCompanyGroup
{
    /** @var string */
    private $id;

    /** @var BasketCompany */
    private $basketCompany;

    /** @var BasketShippingGroup[] */
    private $shippingGroups;

    public function __construct(array $data)
    {
        //$this->id = $data['id'];
        $this->basketCompany = new BasketCompany($data['company']);

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

    public function getBasketCompany(): BasketCompany
    {
        return $this->basketCompany;
    }

    /**
     * @return BasketShippingGroup[]
     */
    public function getShippingGroups(): array
    {
        return $this->shippingGroups;
    }
}
