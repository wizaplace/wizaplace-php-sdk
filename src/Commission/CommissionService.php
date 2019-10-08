<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Commission;

use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\JsonResponse;
use Wizaplace\SDK\AbstractService;

final class CommissionService extends AbstractService
{
    /** @return mixed[] */
    public function addMarketplaceCommission(Commission $commission): array
    {
        $this->client->mustBeAuthenticated();

        return $this->client->post(
            'commissions',
            [
                RequestOptions::JSON => [
                    'percent' => $commission->getPercentAmount(),
                    'fixed' => $commission->getFixedAmount(),
                    'maximum' => $commission->getMaximumAmount(),
                ],
            ]
        );
    }

    public function getMarketplaceCommission(): Commission
    {
        $this->client->mustBeAuthenticated();

        return new Commission($this->client->get('commissions/default'));
    }

    /** @return mixed[] */
    public function deleteCommission(string $commissionId): array
    {
        $this->client->mustBeAuthenticated();

        return $this->client->delete("commissions/$commissionId");
    }
}
