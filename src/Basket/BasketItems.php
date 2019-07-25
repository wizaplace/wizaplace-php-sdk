<?php
/**
 * @author    Wizaplace DevTeam <dev@wizaplace.com
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Basket;

/**
 * Class BasketItems
 * @package Wizaplace\SDK\BasketItems
 */
final class BasketItems
{
    /** @var string */
    private $basketId;

    /** @var array */
    private $items;

    /** @var int */
    private $quantitiesTotal;

    /** @var int */
    private $offset;

    /** @var int */
    private $limit;

    /** @var int */
    private $total;

    public function __construct(array $data)
    {
        $this->basketId = isset($data['basketId']) ? $data['basketId'] : null;
        $this->items = isset($data['items']) ? $data['items'] : [];
        $this->quantitiesTotal = isset($data['quantitiesTotal']) ? (int) $data['quantitiesTotal'] : 0;
        $this->offset = isset($data['offset']) ? (int) $data['offset'] : 0;
        $this->limit = isset($data['limit']) ? (int) $data['limit'] : 0;
        $this->total = isset($data['total']) ? (int) $data['total'] : 0;
    }

    public function getBasketId(): ?string
    {
        return $this->basketId;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getQuantitiesTotal(): int
    {
        return $this->quantitiesTotal;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getTotal(): int
    {
        return $this->total;
    }
}
