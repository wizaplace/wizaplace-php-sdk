<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK;

use Psr\Http\Message\ResponseInterface;

class PaginationHttpHeaders
{
    public const LIMIT = 'x-pagination-limit';
    public const OFFSET = 'x-pagination-offset';
    public const TOTAL = 'x-pagination-total';

    protected $limit;
    protected $offset;
    protected $total;

    public function __construct(ResponseInterface $response)
    {
        $this->limit = $response->getHeader(PaginationHttpHeaders::LIMIT)
            ? (int) $response->getHeader(PaginationHttpHeaders::LIMIT)[0]
            : null;

        $this->offset = $response->getHeader(PaginationHttpHeaders::OFFSET)
            ? (int) $response->getHeader(PaginationHttpHeaders::OFFSET)[0]
            : null;

        $this->total = $response->getHeader(PaginationHttpHeaders::TOTAL)
            ? (int) $response->getHeader(PaginationHttpHeaders::TOTAL)[0]
            : null;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function toArray(): array
    {
        return [
            PaginationHttpHeaders::LIMIT => $this->getLimit(),
            PaginationHttpHeaders::OFFSET => $this->getOffset(),
            PaginationHttpHeaders::TOTAL => $this->getTotal(),
        ];
    }
}
