<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Basket;

final class BasketCompanyGroup
{
    /** @var BasketCompany */
    private $basketCompany;

    /** @var BasketShippingGroup[] */
    private $shippingGroups;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->basketCompany = new BasketCompany($data['company']);

        $this->shippingGroups = array_map(static function (array $shippingGroup) : BasketShippingGroup {
            return new BasketShippingGroup($shippingGroup);
        }, $data['shippingGroups']);
    }

    public function getCompany(): BasketCompany
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
