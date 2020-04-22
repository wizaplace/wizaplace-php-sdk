<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Order;

final class OrderAttachmentFilter
{
    public const LIMIT = 'limit';
    public const OFFSET = 'offset';
    public const TYPE = 'type';


    /** @var null|int */
    private $limit;

    /** @var null|int */
    private $offset;

    /** @var null|OrderAttachmentType */
    private $type;

    public function __construct()
    {
        $this->setLimit(10);
        $this->setOffset(0);
    }

    /** @return mixed[] */
    public function getFilters(): array
    {
        $filters = [
            static::LIMIT => $this->getLimit(),
            static::OFFSET => $this->getOffset(),
            static::TYPE => $this->getType() instanceof OrderAttachmentType ? $this->getType()->getValue() : null,
        ];

        return array_filter(
            $filters,
            function ($item): bool {
                return false === \is_null($item);
            }
        );
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function setOffset(?int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    public function getType(): ?OrderAttachmentType
    {
        return $this->type;
    }

    public function setType(?OrderAttachmentType $type): self
    {
        $this->type = $type;

        return $this;
    }
}
