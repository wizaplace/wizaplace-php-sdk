<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion;

use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;

/**
 * Class BasketPromotionService
 * @package Wizaplace\SDK\Vendor\Promotion
 */
final class BasketPromotionService extends AbstractService
{
    /**
     * @param string $promotionId
     *
     * @return BasketPromotion
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     * @throws \Exception
     */
    public function getPromotion(string $promotionId): BasketPromotion
    {
        $this->client->mustBeAuthenticated();

        $promotionData = $this->client->get('promotions/basket/'.$promotionId);

        return new BasketPromotion($promotionData);
    }

    /**
     * @return BasketPromotion[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function listPromotions(): array
    {
        $this->client->mustBeAuthenticated();

        $responseData = $this->client->get('promotions/basket');

        return array_map(static function (array $promotionData): BasketPromotion {
            return new BasketPromotion($promotionData);
        }, $responseData['promotions']);
    }

    /**
     * @param SaveBasketPromotionCommand $command
     *
     * @return BasketPromotion
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     * @throws \Exception
     */
    public function savePromotion(SaveBasketPromotionCommand $command): BasketPromotion
    {
        $this->client->mustBeAuthenticated();

        $data = $command->toArray();
        $promotionId = $command->getPromotionId();

        if ($promotionId === null) {
            $promotionData = $this->client->post('promotions/basket', [
                RequestOptions::JSON => $data,
            ]);
        } else {
            $promotionData = $this->client->put('promotions/basket/'.$promotionId, [
                RequestOptions::JSON => $data,
            ]);
        }

        return new BasketPromotion($promotionData);
    }

    /**
     * @param string $promotionId
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function deletePromotion(string $promotionId): void
    {
        $this->client->mustBeAuthenticated();

        $this->client->delete('promotions/basket/'.$promotionId);
    }
}
