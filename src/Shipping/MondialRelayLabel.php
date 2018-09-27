<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Shipping;

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

    public function __construct(array $data)
    {
        $this->trackingNumber = $data['tracking_number'];
        $this->labelUrl = $data['label_url'];
    }

    public function getTrackingNumber(): string
    {
        return $this->trackingNumber;
    }

    public function getLabelUrl(): string
    {
        return $this->labelUrl;
    }
}
