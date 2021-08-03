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
use Wizaplace\SDK\Vendor\Promotion\Rules\BasketHasGroupInListRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\BasketPriceSuperiorToRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\BasketRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\MaxUsageCountPerUserRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\MaxUsageCountRule;
use Wizaplace\SDK\Vendor\Promotion\SaveMarketplacePromotionCommand;
use Wizaplace\SDK\Vendor\Promotion\Targets\BasketTarget;
use Wizaplace\SDK\Vendor\Promotion\Targets\CategoriesTarget;
use Wizaplace\SDK\Vendor\Promotion\Targets\ProductsTarget;

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

    public function testCreateMarketplacePromotionWithMaxAmount(): void
    {
        $service = $this->buildMarketplacePromotionService('admin@wizaplace.com', static::VALID_PASSWORD);

        $from = new \DateTimeImmutable((new \DateTimeImmutable('2021-01-28'))->format(\DateTime::RFC3339));
        $to = new \DateTime((new \DateTime('2021-02-02 23:59'))->format(\DateTime::RFC3339));
        $savedPromotion = $service->saveMarketplacePromotion(
            SaveMarketplacePromotionCommand::createNew()
                ->setName('Promotion')
                ->setActive(true)
                ->setCoupon('Coupon WITH MAX AMOUNT')
                ->setDiscounts(
                    [
                        new FixedDiscount(40, 20),
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
        static::assertSame('Promotion', $savedPromotion->getName());

        static::assertContainsOnly(Discount::class, $savedPromotion->getDiscounts());
        static::assertCount(1, $savedPromotion->getDiscounts());
        $fixedDiscount = $savedPromotion->getDiscounts()[0];
        static::assertInstanceOf(FixedDiscount::class, $fixedDiscount);
        static::assertSame(40.0, $fixedDiscount->getValue());
        static::assertSame(20.0, $fixedDiscount->getMaxAmount());
    }

    public function testCreateMarketplacePromotionWithGroups(): void
    {
        $service = $this->buildMarketplacePromotionService('admin@wizaplace.com', static::VALID_PASSWORD);

        $from = new \DateTimeImmutable((new \DateTimeImmutable('2021-04-28'))->format(\DateTime::RFC3339));
        $to = new \DateTime((new \DateTime('2021-04-30 23:59'))->format(\DateTime::RFC3339));
        $savedPromotion = $service->saveMarketplacePromotion(
            SaveMarketplacePromotionCommand::createNew()
                ->setName('summer promotion')
                ->setActive(true)
                ->setCoupon('SUMMER 0')
                ->setDiscounts(
                    [
                        new FixedDiscount(40),
                    ]
                )
                ->setPeriod(new PromotionPeriod($from, $to))
                ->setRule(
                    new AndBasketRule(
                        new BasketPriceSuperiorToRule(100),
                        new BasketHasGroupInListRule(['1c18aafa-9b81-11eb-8d94-0242ac120005'])
                    )
                )
                ->setTarget(new BasketTarget())
        );

        $rootRule = $savedPromotion->getRule();
        static::assertInstanceOf(BasketRule::class, $rootRule);
        static::assertContainsOnly(BasketRule::class, $rootRule->getItems());
        static::assertCount(2, $rootRule->getItems());
        $rule = $rootRule->getItems()[0];
        static::assertInstanceOf(BasketPriceSuperiorToRule::class, $rule);
        static::assertSame(100.0, $rule->getValue());
        $rule = $rootRule->getItems()[1];
        static::assertInstanceOf(BasketHasGroupInListRule::class, $rule);
        static::assertSame(['1c18aafa-9b81-11eb-8d94-0242ac120005'], $rule->getGroupsIds());
    }

    public function testCreateMarketplacePromotionWithTargetProductInBasket(): void
    {
        $service = $this->buildMarketplacePromotionService('admin@wizaplace.com', 'Windows.98');

        $from = new \DateTimeImmutable((new \DateTimeImmutable('2021-06-04'))->format(\DateTime::RFC3339));
        $to = new \DateTime((new \DateTime('2021-06-27 23:59'))->format(\DateTime::RFC3339));
        $savedPromotion = $service->saveMarketplacePromotion(
            SaveMarketplacePromotionCommand::createNew()
                ->setName('Promotion With Target Product In Basket')
                ->setActive(true)
                ->setCoupon('Promotion 1')
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
                ->setTarget(new ProductsTarget(1, 2))
        );

        static::assertSame('product_in_basket', $savedPromotion->getTarget()->getType()->getValue());
        static::assertArrayHasKey('target', $savedPromotion->jsonSerialize());
        static::assertArrayHasKey('products_ids', $savedPromotion->jsonSerialize()['target']);
        static::assertSame([1, 2], $savedPromotion->jsonSerialize()['target']['products_ids']);
        static::assertArrayHasKey('type', $savedPromotion->jsonSerialize()['target']);
        static::assertSame('product_in_basket', $savedPromotion->jsonSerialize()['target']['type']);
    }

    public function testCreateMarketplacePromotionWithTargetProductCategoryInBasket(): void
    {
        $service = $this->buildMarketplacePromotionService('admin@wizaplace.com', 'Windows.98');

        $from = new \DateTimeImmutable((new \DateTimeImmutable('2021-06-04'))->format(\DateTime::RFC3339));
        $to = new \DateTime((new \DateTime('2021-06-27 23:59'))->format(\DateTime::RFC3339));
        $savedPromotion = $service->saveMarketplacePromotion(
            SaveMarketplacePromotionCommand::createNew()
                ->setName('Promotion With Target Product Category In Basket')
                ->setActive(true)
                ->setCoupon('Promotion 2')
                ->setDiscounts(
                    [
                        new FixedDiscount(50),
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
                ->setTarget(new CategoriesTarget(3, 7))
        );

        static::assertSame('product_category_in_basket', $savedPromotion->getTarget()->getType()->getValue());
        static::assertArrayHasKey('target', $savedPromotion->jsonSerialize());
        static::assertArrayHasKey('categories_ids', $savedPromotion->jsonSerialize()['target']);
        static::assertSame([3, 7], $savedPromotion->jsonSerialize()['target']['categories_ids']);
        static::assertArrayHasKey('type', $savedPromotion->jsonSerialize()['target']);
        static::assertSame('product_category_in_basket', $savedPromotion->jsonSerialize()['target']['type']);
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

    public function testUpdateMarketplacePromotionWithMaxAmount(): void
    {
        $service = $this->buildMarketplacePromotionService('admin@wizaplace.com', static::VALID_PASSWORD);

        $from = new \DateTimeImmutable((new \DateTimeImmutable('2021-01-29'))->format(\DateTime::RFC3339));
        $to = new \DateTime((new \DateTime('2021-02-03 23:59'))->format(\DateTime::RFC3339));
        $savedPromotion = $service->saveMarketplacePromotion(
            SaveMarketplacePromotionCommand::createNew()
                ->setName('Promotion with max Amount')
                ->setActive(true)
                ->setCoupon('Coupon With max Amount')
                ->setDiscounts(
                    [
                        new FixedDiscount(40, 20),
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
        $promotionId = $savedPromotion->getPromotionId();

        $updatedPromotion = $service->saveMarketplacePromotion(
            SaveMarketplacePromotionCommand::updateExisting($promotionId)
                ->setName('summer promotion updated')
                ->setActive(false)
        );

        static::assertInstanceOf(MarketplacePromotion::class, $updatedPromotion);
        static::assertSame(20.0, $updatedPromotion->getDiscounts()[0]->getMaxAmount());
    }

    public function testUpdateMarketplacePromotionWithGroups(): void
    {
        $service = $this->buildMarketplacePromotionService('admin@wizaplace.com', static::VALID_PASSWORD);

        $updatedPromotion = $service->saveMarketplacePromotion(
            SaveMarketplacePromotionCommand::updateExisting('f6a441d8-95fa-44ac-9bc7-ca7f0fc980d9')
                ->setRule(new BasketHasGroupInListRule(['1c18aafa-9b81-11eb-8d94-0242ac120005']))
        );

        static::assertInstanceOf(MarketplacePromotion::class, $updatedPromotion);
        $rootRule = $updatedPromotion->getRule();
        static::assertInstanceOf(BasketHasGroupInListRule::class, $rootRule);
        static::assertSame(['1c18aafa-9b81-11eb-8d94-0242ac120005'], $rootRule->getGroupsIds());
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

    public function testGetMarketplacePromotionsWithGroups(): void
    {
        $service = $this->buildMarketplacePromotionService('admin@wizaplace.com', static::VALID_PASSWORD);

        $promotionList = $service->getMarketplacePromotionsList();

        static::assertInstanceOf(MarketplacePromotionsList::class, $promotionList);
        static::assertContainsOnly(MarketplacePromotion::class, $promotionList->getItems());
        $rootRule = $promotionList->getItems()[0]->getRule();
        static::assertInstanceOf(BasketRule::class, $rootRule);
        static::assertContainsOnly(BasketRule::class, $rootRule->getItems());
        static::assertCount(2, $rootRule->getItems());
        $rule = $rootRule->getItems()[1];
        static::assertInstanceOf(BasketHasGroupInListRule::class, $rule);
        static::assertSame(['1c18aafa-9b81-11eb-8d94-0242ac120005'], $rule->getGroupsIds());
    }

    public function testGetMarketplacePromotionsWithTargetProductInBasket(): void
    {
        $service = $this->buildMarketplacePromotionService('admin@wizaplace.com', 'Windows.98');

        $promotionList = $service->getMarketplacePromotionsList();

        static::assertSame('product_in_basket', $promotionList->getItems()[2]->getTarget()->getType()->getValue());
        static::assertArrayHasKey('target', $promotionList->getItems()[2]->jsonSerialize());
        static::assertArrayHasKey('products_ids', $promotionList->getItems()[2]->jsonSerialize()['target']);
        static::assertSame([1, 2], $promotionList->getItems()[2]->jsonSerialize()['target']['products_ids']);
        static::assertArrayHasKey('type', $promotionList->getItems()[2]->jsonSerialize()['target']);
        static::assertSame('product_in_basket', $promotionList->getItems()[2]->jsonSerialize()['target']['type']);
    }

    public function testGetMarketplacePromotionsWithTargetProductCategoryInBasket(): void
    {
        $service = $this->buildMarketplacePromotionService('admin@wizaplace.com', 'Windows.98');

        $promotionList = $service->getMarketplacePromotionsList();

        static::assertSame('product_category_in_basket', $promotionList->getItems()[1]->getTarget()->getType()->getValue());
        static::assertArrayHasKey('target', $promotionList->getItems()[1]->jsonSerialize());
        static::assertArrayHasKey('categories_ids', $promotionList->getItems()[1]->jsonSerialize()['target']);
        static::assertSame([3, 7], $promotionList->getItems()[1]->jsonSerialize()['target']['categories_ids']);
        static::assertArrayHasKey('type', $promotionList->getItems()[1]->jsonSerialize()['target']);
        static::assertSame('product_category_in_basket', $promotionList->getItems()[1]->jsonSerialize()['target']['type']);
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

    public function testGetMarketplacePromotionsWithMaxAmount(): void
    {
        $service = $this->buildMarketplacePromotionService('admin@wizaplace.com', static::VALID_PASSWORD);

        $promotionList = $service->getMarketplacePromotionsList();
        foreach ($promotionList->getItems() as $promotion) {
            static::ArrayHasKey('maxAmount', $promotion->getDiscounts()[0]);
        }
        //maxAmount = 30
        static::assertSame(50.0, $promotionList->getItems()[0]->getDiscounts()[0]->getValue());
        static::assertSame(30.0, $promotionList->getItems()[0]->getDiscounts()[0]->getMaxAmount());
        //maxAmount = null
        static::assertSame(50.0, $promotionList->getItems()[1]->getDiscounts()[0]->getValue());
        static::assertNull($promotionList->getItems()[1]->getDiscounts()[0]->getMaxAmount());
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
