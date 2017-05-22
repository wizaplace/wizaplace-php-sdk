<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

namespace Wizaplace\Basket;

class BasketShippingGroup
{
    /** @var string */
    private $id;

    /** @var BasketItem[] */
    private $items;

    /** @var Shipping[] */
    private $shippings;

    public function __construct(array $data)
    {
        //$this->id = $data['id'];

        $this->items = array_map(
            function ($item) {
                return new BasketItem($item);
            },
            $data['items']
        );

        $this->shippings = array_map(
            function ($shipping) {
                return new Shipping($shipping);
            },
            $data['shippings']
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return BasketItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return Shipping[]
     */
    public function getShippings(): array
    {
        return $this->shippings;
    }
}
