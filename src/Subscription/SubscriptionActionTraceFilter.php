<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Subscription;

class SubscriptionActionTraceFilter
{
    public const LIMIT = 'limit';
    public const OFFSET = 'offset';

    /** @var null|int */
    private $limit;

    /** @var null|int */
    private $offset;

    public function getFilters(): array
    {
        $filters = [
            static::LIMIT => $this->getLimit(),
            static::OFFSET => $this->getOffset(),
        ];

        return array_filter(
            $filters,
            static function ($item): bool {
                return \is_null($item) === false;
            }
        );
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function setOffset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }
}
