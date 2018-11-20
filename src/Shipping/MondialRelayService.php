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
        $results = $this->client->get('mondial-relay/points-relais', [
            RequestOptions::QUERY => [
                'zipCode' => $zipCode,
            ],
        ]);

        return array_map(function ($elt) {
            return new MondialRelayPoint($elt);
        }, $results);
    }

    public function getPickupPoint(string $pickupPointId): MondialRelayPoint
    {
        $this->client->mustBeAuthenticated();

        $result = $this->client->get('mondial-relay/points-relais/'.$pickupPointId);

        return new MondialRelayPoint($result);
    }

    public function getBrandCode(): MondialRelayBrandCode
    {
        $result = $this->client->get('mondial-relay/brand-code');

        return new MondialRelayBrandCode($result['brandCode']);
    }
}
