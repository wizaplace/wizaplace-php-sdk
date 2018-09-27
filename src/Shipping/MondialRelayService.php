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
}
