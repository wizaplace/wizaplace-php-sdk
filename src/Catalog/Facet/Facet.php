<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog\Facet;

use function theodorejb\polycast\to_string;

/**
 * Class Facet
 * @package Wizaplace\SDK\Catalog\Facet
 */
abstract class Facet
{
    /** @var string */
    private $name;
    /** @var string */
    private $label;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->name = to_string($data['name']); // string cast is necessary because the API sometimes sends IDs as integers
        $this->label = $data['label'];
    }

    /**
     * @param array $data
     *
     * @return Facet
     */
    public static function buildFromJson(array $data): self
    {
        if ($data['isNumeric']) {
            return new NumericFacet($data);
        }

        return new ListFacet($data);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }
}
