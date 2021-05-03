<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

final class CompanyFilter
{
    public const EXTRA = 'extra';

    /** @var array|null */
    private $extra;

    /** @return array|null */
    public function getExtra(): ?array
    {
        return $this->extra;
    }

    public function setExtra(array $extra): self
    {
        $this->extra = $extra;

        return $this;
    }

    public function getFilters(): array
    {
        $filters = [
            static::EXTRA => $this->getExtra(),
        ];

        return \array_filter(
            $filters,
            static function ($item): bool {
                return \is_null($item) === false;
            }
        );
    }
}
