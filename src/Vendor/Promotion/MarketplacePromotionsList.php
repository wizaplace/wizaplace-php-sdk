<?php

/**
 * @author    Wizaplace DevTeam <dev@wizaplace.com
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion;

/**
 * Manage a list of marketplacePromotions with pagination data
 */
final class MarketplacePromotionsList
{
    /** @var MarketplacePromotion[] */
    private $items;

    /** @var int */
    private $offset;

    /** @var int */
    private $limit;

    /** @var int */
    private $total;

    public function __construct(array $data)
    {
        $this->items = $this->denormalizeItems($data['items']);
        $this->offset = isset($data['offset']) ? (int) $data['offset'] : 0;
        $this->limit = isset($data['limit']) ? (int) $data['limit'] : 0;
        $this->total = isset($data['total']) ? (int) $data['total'] : 0;
    }

    /** @return MarketplacePromotion[] */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    /** @return MarketplacePromotion[] */
    private function denormalizeItems(array $items): array
    {
        return array_map(
            static function (array $item): MarketplacePromotion {
                return new MarketplacePromotion($item);
            },
            $items
        );
    }
}
