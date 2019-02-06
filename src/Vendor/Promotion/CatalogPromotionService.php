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
 * Class CatalogPromotionService
 * @package Wizaplace\SDK\Vendor\Promotion
 */
final class CatalogPromotionService extends AbstractService
{
    /**
     * @param string $promotionId
     *
     * @return CatalogPromotion
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getPromotion(string $promotionId): CatalogPromotion
    {
        $this->client->mustBeAuthenticated();

        $promotionData = $this->client->get('promotions/catalog/'.$promotionId);

        return new CatalogPromotion($promotionData);
    }

    /**
     * @return CatalogPromotion[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function listPromotions(): array
    {
        $this->client->mustBeAuthenticated();

        $responseData = $this->client->get('promotions/catalog');

        return array_map(static function (array $promotionData): CatalogPromotion {
            return new CatalogPromotion($promotionData);
        }, $responseData['promotions']);
    }

    /**
     * @param SaveCatalogPromotionCommand $command
     *
     * @return CatalogPromotion
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     * @throws \Exception
     */
    public function savePromotion(SaveCatalogPromotionCommand $command): CatalogPromotion
    {
        $this->client->mustBeAuthenticated();

        $data = $command->toArray();
        $promotionId = $command->getPromotionId();

        if ($promotionId === null) {
            $promotionData = $this->client->post('promotions/catalog', [
                RequestOptions::JSON => $data,
            ]);
        } else {
            $promotionData = $this->client->put('promotions/catalog/'.$promotionId, [
                RequestOptions::JSON => $data,
            ]);
        }

        return new CatalogPromotion($promotionData);
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

        $this->client->delete('promotions/catalog/'.$promotionId);
    }
}
