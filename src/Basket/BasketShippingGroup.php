<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Basket;

use function theodorejb\polycast\to_int;

final class BasketShippingGroup
{
    /** @var int */
    private $id;

    /** @var BasketItem[] */
    private $items;

    /** @var Shipping[] */
    private $shippings;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->id = to_int($data['id']);

        $this->items = array_map(static function (array $item) : BasketItem {
            return new BasketItem($item);
        }, $data['items']);

        $this->shippings = array_map(static function (array $shipping) : Shipping {
            return new Shipping($shipping);
        }, $data['shippings']);
    }

    public function getId(): int
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
