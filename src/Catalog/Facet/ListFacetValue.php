<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Catalog\Facet;

use function theodorejb\polycast\to_int;

final class ListFacetValue
{
    /** @var string */
    private $label;

    /** @var int */
    private $count;

    /** @var int */
    private $position;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->label = $data['label'];
        $this->count = to_int($data['count']);
        $this->position = to_int($data['position'] ?? 0);
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
