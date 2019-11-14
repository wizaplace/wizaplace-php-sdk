<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion;

use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;

final class MarketplacePromotionService extends AbstractService
{
    public function getMarketplacePromotionsList(
        int $offset = 0,
        int $limit = 10,
        MarketplacePromotionsListFilter $filter = null
    ): MarketplacePromotionsList {
        $this->client->mustBeAuthenticated();

        return new MarketplacePromotionsList(
            $this->client->get(
                'promotions/marketplace?'.http_build_query(array_merge([
                    'offset' => $offset,
                    'limit' => $limit,
                ], \is_null($filter) ? [] : $filter->toArray()))
            )
        );
    }

    public function saveMarketplacePromotion(SaveMarketplacePromotionCommand $command): MarketplacePromotion
    {
        $this->client->mustBeAuthenticated();

        $data = $command->toArray();
        $promotionId = $command->getPromotionId();

        if ($promotionId === null) {
            $promotionData = $this->client->post('promotions/marketplace', [
                RequestOptions::JSON => $data,
            ]);
        } else {
            $promotionData = $this->client->patch('promotions/marketplace/'.$promotionId, [
                RequestOptions::JSON => $data,
            ]);
        }

        return new MarketplacePromotion($promotionData);
    }
}
