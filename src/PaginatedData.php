<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK;

final class PaginatedData
{
    /** @var int */
    private $limit;

    /** @var int */
    private $offset;

    /** @var int */
    private $total;

    /** @var array */
    private $items;

    public function __construct(int $limit, int $offset, int $total, array $items)
    {
        $this->limit = $limit;
        $this->offset = $offset;
        $this->total = $total;
        $this->items = $items;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getItems(): array
    {
        return $this->items;
    }
}
