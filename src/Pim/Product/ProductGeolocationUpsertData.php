<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

final class ProductGeolocationUpsertData
{
    /** @var float */
    private $latitude;

    /** @var float */
    private $longitude;

    /** @var string */
    private $label;

    /** @var string */
    private $zipcode;

    public function __construct(float $latitude, float $longitude)
    {
        $this->setLatitude($latitude);
        $this->setLongitude($longitude);
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function setZipcode(string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * @internal
     * @return array
     */
    public function toArray(): array
    {
        $data = [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];

        if ($this->zipcode !== null) {
            $data['zipcode'] = $this->zipcode;
        }

        if ($this->label !== null) {
            $data['label'] = $this->label;
        }


        return $data;
    }
}
