<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Shipping;

use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;

final class MondialRelayService extends AbstractService
{
    public function searchPickupPoints(string $zipCode): array
    {
        return $this->client->get('mondial-relay/points-relais', [
            RequestOptions::QUERY => [
                'zipCode' => $zipCode,
            ],
        ]);
    }

    public function getPickupPoint(string $pickupPointId): MondialRelayPoint
    {
        $result = $this->client->get('mondial-relay/points-relais/'.$pickupPointId);

        return new MondialRelayPoint($result);
    }

    public function getBrandCode(): MondialRelayBrandCode
    {
        $result = $this->client->get('mondial-relay/brand-code');

        return new MondialRelayBrandCode($result['brandCode']);
    }
}
