<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Vendor\Promotion;

use Wizaplace\SDK\Tests\ApiTestCase;
use Wizaplace\SDK\Vendor\Promotion\Discounts\Discount;
use Wizaplace\SDK\Vendor\Promotion\Discounts\FixedDiscount;
use Wizaplace\SDK\Vendor\Promotion\Discounts\PercentageDiscount;
use Wizaplace\SDK\Vendor\Promotion\MarketplacePromotion;
use Wizaplace\SDK\Vendor\Promotion\MarketplacePromotionsList;
use Wizaplace\SDK\Vendor\Promotion\MarketplacePromotionService;
use Wizaplace\SDK\Vendor\Promotion\MarketplacePromotionsListFilter;
use Wizaplace\SDK\Vendor\Promotion\PromotionPeriod;
use Wizaplace\SDK\Vendor\Promotion\Rules\AndBasketRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\BasketPriceSuperiorToRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\BasketRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\MaxUsageCountPerUserRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\MaxUsageCountRule;
use Wizaplace\SDK\Vendor\Promotion\SaveMarketplacePromotionCommand;
use Wizaplace\SDK\Vendor\Promotion\Targets\BasketTarget;

class MarketplacePromotionServiceTest extends ApiTestCase
{
    private const PROMOTION_ID = 'd8df258d-ca0d-41c2-ba6b-e69e5a69e9c5';

    public function testCreateMarketplacePromotion(): void
    {
        $service = $this->buildMarketplacePromotionService();

        $from = new \DateTimeImmutable((new \DateTimeImmutable('2019-09-07'))->format(\DateTime::RFC3339));
        $to = new \DateTime((new \DateTime('2019-09-27 23:59'))->format(\DateTime::RFC3339));
        $savedPromotion = $service->saveMarketplacePromotion(
            SaveMarketplacePromotionCommand::createNew()
                ->setName('summer promotion')
                ->setActive(true)
                ->setCoupon('SUMMER')
                ->setDiscounts(
                    [
                        new FixedDiscount(40),
                    ]
                )
                ->setPeriod(new PromotionPeriod($from, $to))
                ->setRule(
                    new AndBasketRule(
                        new BasketPriceSuperiorToRule(100),
                        new MaxUsageCountRule(100),
                        new MaxUsageCountPerUserRule(1)
                    )
                )
                ->setTarget(new BasketTarget())
        );

        static::assertInstanceOf(MarketplacePromotion::class, $savedPromotion);
        static::assertInternalType('string', $savedPromotion->getPromotionId());
        static::assertNotEmpty($savedPromotion->getPromotionId());
        static::assertSame('summer promotion', $savedPromotion->getName());
        static::assertSame(true, $savedPromotion->isActive());

        static::assertInstanceOf(PromotionPeriod::class, $savedPromotion->getPeriod());
        static::assertSame($from->getTimestamp(), $savedPromotion->getPeriod()->getFrom()->getTimestamp());
        static::assertSame($to->getTimestamp(), $savedPromotion->getPeriod()->getTo()->getTimestamp());

        static::assertContainsOnly(Discount::class, $savedPromotion->getDiscounts());
        static::assertCount(1, $savedPromotion->getDiscounts());
        $fixedDiscount = $savedPromotion->getDiscounts()[0];
        static::assertInstanceOf(FixedDiscount::class, $fixedDiscount);
        static::assertSame(40.0, $fixedDiscount->getValue());

        $rootRule = $savedPromotion->getRule();
        static::assertInstanceOf(BasketRule::class, $rootRule);
        static::assertInstanceOf(AndBasketRule::class, $rootRule);
        static::assertContainsOnly(BasketRule::class, $rootRule->getItems());
        static::assertCount(3, $rootRule->getItems());

        $productPriceSuperior = $rootRule->getItems()[0];
        static::assertInstanceOf(BasketPriceSuperiorToRule::class, $productPriceSuperior);
        static::assertSame(100.0, $productPriceSuperior->getValue());

        $maxUsageCount = $rootRule->getItems()[1];
        static::assertInstanceOf(MaxUsageCountRule::class, $maxUsageCount);
        static::assertSame(100, $maxUsageCount->getValue());

        $maxUsageCountPerUser = $rootRule->getItems()[2];
        static::assertInstanceOf(MaxUsageCountPerUserRule::class, $maxUsageCountPerUser);
        static::assertSame(1, $maxUsageCountPerUser->getValue());
    }

    public function testUpdateMarketplacePromotion(): void
    {
        $service = $this->buildMarketplacePromotionService();

        $updatedPromotion = $service->saveMarketplacePromotion(
            SaveMarketplacePromotionCommand::updateExisting(static::PROMOTION_ID)
                ->setName('summer promotion updated')
                ->setActive(false)
        );

        static::assertInstanceOf(MarketplacePromotion::class, $updatedPromotion);
        static::assertSame(static::PROMOTION_ID, $updatedPromotion->getPromotionId());
        static::assertSame('summer promotion updated', $updatedPromotion->getName());
        static::assertSame(false, $updatedPromotion->isActive());
    }

    public function testGetMarketplacePromotions(): void
    {
        $service = $this->buildMarketplacePromotionService();

        $promotionList = $service->getMarketplacePromotionsList();

        static::assertInstanceOf(MarketplacePromotionsList::class, $promotionList);
        static::assertSame(0, $promotionList->getOffset());
        static::assertSame(10, $promotionList->getLimit());
        static::assertSame(1, $promotionList->getTotal());
        static::assertCount(1, $promotionList->getItems());
        static::assertContainsOnly(MarketplacePromotion::class, $promotionList->getItems());
    }

    public function testGetListMarketplacePromotionsFiltered(): void
    {
        $service = $this->buildMarketplacePromotionService();

        $list = $service->getMarketplacePromotionsList(0, 10, new MarketplacePromotionsListFilter('FIRST'));
        static::assertSame(1, $list->getTotal());
        static::assertCount(1, $list->getItems());
        static::assertSame('FIRST', $list->getItems()[0]->getCoupon());

        $list = $service->getMarketplacePromotionsList(0, 10, new MarketplacePromotionsListFilter(null, false));
        static::assertSame(2, $list->getTotal());
        static::assertCount(2, $list->getItems());
        static::assertSame('SECOND', $list->getItems()[0]->getCoupon());
        static::assertSame('THIRD', $list->getItems()[1]->getCoupon());

        $list = $service->getMarketplacePromotionsList(0, 10, new MarketplacePromotionsListFilter(null, false, false));
        static::assertSame(1, $list->getTotal());
        static::assertCount(1, $list->getItems());
        static::assertSame('THIRD', $list->getItems()[0]->getCoupon());
        static::assertSame(false, $list->getItems()[0]->isActive());
        static::assertSame(false, $list->getItems()[0]->isValid());
    }

    private function createSimpleMarketplacePromotion(
        MarketplacePromotionService $service,
        string $coupon,
        bool $isActive = true,
        PromotionPeriod $period = null
    ): MarketplacePromotion {
        $period = $period ?? new PromotionPeriod(
            new \DateTimeImmutable(),
            new \DateTime('+1 day')
        );

        return $service->saveMarketplacePromotion(
            SaveMarketplacePromotionCommand::createNew()
                ->setName($coupon)
                ->setActive($isActive)
                ->setCoupon($coupon)
                ->setDiscounts(
                    [
                        new FixedDiscount(10),
                    ]
                )
                ->setPeriod($period)
                ->setTarget(new BasketTarget())
        );
    }

    private function buildMarketplacePromotionService(string $email = 'admin@wizaplace.com', string $password = 'password'): MarketplacePromotionService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new MarketplacePromotionService($apiClient);
    }
}
