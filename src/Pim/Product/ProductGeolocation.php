<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

final class ProductGeolocation
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
     */
    public function __construct(array $data)
    {
        $this->latitude = $data['latitude'];
        $this->longitude = $data['longitude'];
        $this->label = $data['label'];
        $this->zipcode = $data['zipcode'];
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getZipcode(): string
    {
        return $this->zipcode;
    }
}
