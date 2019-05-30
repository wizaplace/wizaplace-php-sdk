<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

/**
 * Class GeoFilter
 * @package Wizaplace\SDK\Catalog
 */
final class GeoFilter
{
    /**
     * @var float
     */
    private $latitude;

    /**
     * @var float
     */
    private $longitude;

    /**
     * @var null|int
     */
    private $radius;

    /**
     * GeoFilter constructor.
     *
     * @param float    $latitude
     * @param float    $longitude
     * @param int|null $radius
     */
    public function __construct(float $latitude, float $longitude, ?int $radius = null)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->radius = $radius;
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
     * @return int|null
     */
    public function getRadius(): ?int
    {
        return $this->radius;
    }
}
