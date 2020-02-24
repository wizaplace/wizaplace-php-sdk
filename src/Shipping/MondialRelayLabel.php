<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Shipping;

/**
 * Class MondialRelayLabel
 * @package Wizaplace\SDK\Shipping
 */
class MondialRelayLabel
{
    /**
     * @var string
     */
    private $trackingNumber;

    /**
     * @var string
     */
    private $labelUrl;

    /**
     * MondialRelayLabel constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->trackingNumber = $data['tracking_number'];
        $this->labelUrl = $data['label_url'];
    }

    /**
     * @return string
     */
    public function getTrackingNumber(): string
    {
        return $this->trackingNumber;
    }

    /**
     * @return string
     */
    public function getLabelUrl(): string
    {
        return $this->labelUrl;
    }
}
