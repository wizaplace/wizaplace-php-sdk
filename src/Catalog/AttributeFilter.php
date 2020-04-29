<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

/**
 * Class AttributeFilter
 * @package Wizaplace\SDK\Catalog
 */
final class AttributeFilter
{
    public const ID = 'id';
    public const CODE = 'code';

    /** @var int[]  */
    private $ids = [];

    /** @var string[]  */
    private $codes = [];

    public function getFilters(): array
    {
        $filters = [
            static::ID => $this->ids,
            static::CODE => $this->codes,
        ];

        array_filter(
            $filters,
            function ($item) {
                return empty($item) === false;
            }
        );

        return $filters;
    }

    /** @return int[] */
    public function getIds(): array
    {
        return $this->ids;
    }

    public function setIds(array $id): self
    {
        $this->ids = $id;

        return $this;
    }

    /** @return string[] */
    public function getCodes(): array
    {
        return $this->codes;
    }

    public function setCodes(array $codes): self
    {
        $this->codes = $codes;

        return $this;
    }
}
