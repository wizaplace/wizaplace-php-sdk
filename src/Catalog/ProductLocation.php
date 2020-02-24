<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

/**
 * Class ProductLocation
 * @package Wizaplace\SDK\Catalog
 */
final class ProductLocation
{
    /** @var float */
    private $latitude;

    /** @var float */
    private $longitude;

    /** @var string */
    private $label;

    /** @var string */
    private $zipcode;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->latitude = $data['latitude'];
        $this->longitude = $data['longitude'];
        $this->label = $data['label'];
        $this->zipcode = $data['zipcode'];
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getZipcode(): string
    {
        return $this->zipcode;
    }
}
