<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Vendor\Promotion;

use Wizaplace\SDK\Exception\PromotionNotFound;
use Wizaplace\SDK\Tests\ApiTestCase;
use Wizaplace\SDK\Vendor\Promotion\BasketPromotion;
use Wizaplace\SDK\Vendor\Promotion\BasketPromotionService;
use Wizaplace\SDK\Vendor\Promotion\Discounts\Discount;
use Wizaplace\SDK\Vendor\Promotion\Discounts\FixedDiscount;
use Wizaplace\SDK\Vendor\Promotion\Discounts\PercentageDiscount;
use Wizaplace\SDK\Vendor\Promotion\PromotionPeriod;
use Wizaplace\SDK\Vendor\Promotion\Rules\AndBasketRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\BasketHasGroupInListRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\BasketHasProductInListRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\BasketPriceInferiorToRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\BasketPriceSuperiorToRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\BasketQuantityInferiorOrEqualToRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\BasketQuantityInferiorToRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\BasketQuantitySuperiorOrEqualToRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\BasketQuantitySuperiorToRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\BasketRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\MaxUsageCountPerUserRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\MaxUsageCountRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\OrBasketRule;
use Wizaplace\SDK\Vendor\Promotion\SaveBasketPromotionCommand;
use Wizaplace\SDK\Vendor\Promotion\Targets\BasketTarget;
use Wizaplace\SDK\Vendor\Promotion\Targets\ProductsTarget;
use Wizaplace\SDK\Vendor\Promotion\Targets\ShippingTarget;
use Wizaplace\SDK\Vendor\Promotion\Targets\BasketPromotionTarget;

final class BasketPromotionServiceTest extends ApiTestCase
{
    public function testPromotionLifeCycle(): void
    {
        $service = $this->buildBasketPromotionService();

        $promotions = $service->listPromotions();
        $this->assertCount(0, $promotions);

        $from = new \DateTimeImmutable('1992-09-07T00:00:00+0000');
        $to = new \DateTime('@1546300800');
        $savedPromotion = $service->savePromotion(
            SaveBasketPromotionCommand::createNew()
                ->setName('test promotion')
                ->setActive(true)
                ->setDiscounts(
                    [
                        new PercentageDiscount(2),
                        new FixedDiscount(3.5),
                    ]
                )
                ->setPeriod(new PromotionPeriod($from, $to))
                ->setRule(
                    new AndBasketRule(
                        new BasketPriceSuperiorToRule(3.13),
                        new BasketPriceInferiorToRule(3.15),
                        new OrBasketRule(
                            new BasketHasProductInListRule(1, 2, 3),
                            new BasketHasProductInListRule(4, 5, 7)
                        ),
                        new MaxUsageCountRule(100),
                        new MaxUsageCountPerUserRule(1)
                    )
                )
                ->setTarget(new ProductsTarget(1, 4, 7))
        );

        // We check that the promotion and company IDs were set.
        $this->assertInternalType('string', $savedPromotion->getPromotionId());
        $this->assertNotEmpty($savedPromotion->getPromotionId());
        $this->assertInternalType('int', $savedPromotion->getCompanyId());
        $this->assertGreaterThan(0, $savedPromotion->getCompanyId());
        // We check that the promotion we got back matches what we wanted to save.
        $this->assertInstanceOf(BasketPromotion::class, $savedPromotion);
        $this->assertSame('test promotion', $savedPromotion->getName());
        $this->assertSame(true, $savedPromotion->isActive());
        $this->assertInstanceOf(PromotionPeriod::class, $savedPromotion->getPeriod());
        $this->assertInstanceOf(\DateTimeInterface::class, $savedPromotion->getPeriod()->getFrom());
        $this->assertInstanceOf(\DateTimeInterface::class, $savedPromotion->getPeriod()->getTo());
        $this->assertSame($from->getTimestamp(), $savedPromotion->getPeriod()->getFrom()->getTimestamp());
        $this->assertSame($to->getTimestamp(), $savedPromotion->getPeriod()->getTo()->getTimestamp());
        $this->assertContainsOnly(Discount::class, $savedPromotion->getDiscounts());
        $this->assertCount(2, $savedPromotion->getDiscounts());
        /** @var PercentageDiscount $firstDiscount */
        $firstDiscount = $savedPromotion->getDiscounts()[0];
        $this->assertInstanceOf(PercentageDiscount::class, $firstDiscount);
        $this->assertSame(2., $firstDiscount->getPercentage());
        /** @var FixedDiscount $secondDiscount */
        $secondDiscount = $savedPromotion->getDiscounts()[1];
        $this->assertInstanceOf(FixedDiscount::class, $secondDiscount);
        $this->assertSame(3.5, $secondDiscount->getValue());
        /** @var AndBasketRule $rootRule */
        $rootRule = $savedPromotion->getRule();
        $this->assertInstanceOf(BasketRule::class, $rootRule);
        $this->assertInstanceOf(AndBasketRule::class, $rootRule);
        $this->assertContainsOnly(BasketRule::class, $rootRule->getItems());
        $this->assertCount(5, $rootRule->getItems());
        /** @var BasketPriceSuperiorToRule $productPriceSuperior */
        $productPriceSuperior = $rootRule->getItems()[0];
        $this->assertInstanceOf(BasketPriceSuperiorToRule::class, $productPriceSuperior);
        $this->assertSame(3.13, $productPriceSuperior->getValue());
        /** @var BasketPriceInferiorToRule $productPriceInferior */
        $productPriceInferior = $rootRule->getItems()[1];
        $this->assertInstanceOf(BasketPriceInferiorToRule::class, $productPriceInferior);
        $this->assertSame(3.15, $productPriceInferior->getValue());
        /** @var OrBasketRule $or */
        $or = $rootRule->getItems()[2];
        $this->assertInstanceOf(OrBasketRule::class, $or);
        $this->assertContainsOnly(BasketRule::class, $or->getItems());
        $this->assertCount(2, $or->getItems());
        /** @var BasketHasProductInListRule $productInList */
        $productInList = $or->getItems()[0];
        $this->assertInstanceOf(BasketHasProductInListRule::class, $productInList);
        $this->assertSame([1, 2, 3], $productInList->getProductsIds());
        /** @var BasketHasProductInListRule $productInList */
        $productInList = $or->getItems()[1];
        $this->assertInstanceOf(BasketHasProductInListRule::class, $productInList);
        $this->assertSame([4, 5, 7], $productInList->getProductsIds());
        /** @var MaxUsageCountRule $maxUsageCount */
        $maxUsageCount = $rootRule->getItems()[3];
        $this->assertInstanceOf(MaxUsageCountRule::class, $maxUsageCount);
        $this->assertSame(100, $maxUsageCount->getValue());
        /** @var MaxUsageCountPerUserRule $maxUsageCountPerUser */
        $maxUsageCountPerUser = $rootRule->getItems()[4];
        $this->assertInstanceOf(MaxUsageCountPerUserRule::class, $maxUsageCountPerUser);
        $this->assertSame(1, $maxUsageCountPerUser->getValue());

        $jsonSerialization = json_encode($savedPromotion);
        $this->assertGreaterThan(2, \strlen($jsonSerialization));

        $actualPromotion = $service->getPromotion($savedPromotion->getPromotionId());
        $this->assertEquals($savedPromotion, $actualPromotion);

        // Let's update a few fields, but not all of them
        $updatedPromotion = $service->savePromotion(
            SaveBasketPromotionCommand::updateExisting($savedPromotion->getPromotionId())
                ->setName('test promotion updated')
                ->setActive(false)
        );

        $this->assertSame($savedPromotion->getPromotionId(), $updatedPromotion->getPromotionId());
        $this->assertInstanceOf(BasketPromotion::class, $updatedPromotion);
        $this->assertSame('test promotion updated', $updatedPromotion->getName());
        $this->assertSame(false, $updatedPromotion->isActive());

        $actualPromotion = $service->getPromotion($savedPromotion->getPromotionId());
        $this->assertEquals($updatedPromotion, $actualPromotion);

        $promotions = $service->listPromotions();
        $this->assertCount(1, $promotions);
        $this->assertContainsOnly(BasketPromotion::class, $promotions);
        $this->assertEquals($actualPromotion, $promotions[0]);

        $service->deletePromotion($actualPromotion->getPromotionId());

        $promotions = $service->listPromotions();
        $this->assertCount(0, $promotions);

        $this->expectException(PromotionNotFound::class);
        $service->getPromotion($savedPromotion->getPromotionId());
    }

    public function testPromotionOnQuantity(): void
    {
        $service = $this->buildBasketPromotionService();

        $promotions = $service->listPromotions();
        $this->assertCount(0, $promotions);

        // Save a promotion with two rules on basket quantity
        $from = new \DateTimeImmutable('1992-09-07T00:00:00+0000');
        $to = new \DateTime('@1546300800');
        $savedPromotion = $service->savePromotion(
            SaveBasketPromotionCommand::createNew()
                ->setName('test promotion')
                ->setActive(true)
                ->setDiscounts(
                    [
                        new PercentageDiscount(2),
                        new FixedDiscount(3.5),
                    ]
                )
                ->setPeriod(new PromotionPeriod($from, $to))
                ->setRule(
                    new AndBasketRule(
                        new BasketQuantitySuperiorToRule(5),
                        new BasketQuantityInferiorToRule(7)
                    )
                )
                ->setTarget(new ProductsTarget(1, 4, 7))
        );

        /** @var AndBasketRule $rootRule */
        $rootRule = $savedPromotion->getRule();
        $this->assertCount(2, $rootRule->getItems());

        // Test the two 'strictly inferior/superior to' rules
        /** @var BasketQuantitySuperiorToRule $productQuantitySuperior */
        $productQuantitySuperior = $rootRule->getItems()[0];
        $this->assertInstanceOf(BasketQuantitySuperiorToRule::class, $productQuantitySuperior);
        $this->assertSame(5, $productQuantitySuperior->getValue());
        /** @var BasketQuantityInferiorToRule $productQuantityInferior */
        $productQuantityInferior = $rootRule->getItems()[1];
        $this->assertInstanceOf(BasketQuantityInferiorToRule::class, $productQuantityInferior);
        $this->assertSame(7, $productQuantityInferior->getValue());

        // Replace old rules by two new rules
        $updatedPromotion = $service->savePromotion(
            SaveBasketPromotionCommand::updateExisting($savedPromotion->getPromotionId())
                ->setRule(
                    new AndBasketRule(
                        new BasketQuantitySuperiorOrEqualToRule(10),
                        new BasketQuantityInferiorOrEqualToRule(12)
                    )
                )
        );

        /** @var AndBasketRule $rootRule */
        $rootRule = $updatedPromotion->getRule();
        $this->assertCount(2, $rootRule->getItems());

        // Test the two 'inferior/superior or equal to' rules
        /** @var BasketQuantitySuperiorOrEqualToRule $productQuantitySuperiorOrEqual */
        $productQuantitySuperiorOrEqual = $rootRule->getItems()[0];
        $this->assertInstanceOf(BasketQuantitySuperiorOrEqualToRule::class, $productQuantitySuperiorOrEqual);
        $this->assertSame(10, $productQuantitySuperiorOrEqual->getValue());
        /** @var BasketQuantityInferiorOrEqualToRule $productQuantityInferiorOrEqual */
        $productQuantityInferiorOrEqual = $rootRule->getItems()[1];
        $this->assertInstanceOf(BasketQuantityInferiorOrEqualToRule::class, $productQuantityInferiorOrEqual);
        $this->assertSame(12, $productQuantityInferiorOrEqual->getValue());

        // Test the get promotion
        $actualPromotion = $service->getPromotion($savedPromotion->getPromotionId());
        $this->assertEquals($updatedPromotion, $actualPromotion);

        // Remove promotion
        $service->deletePromotion($actualPromotion->getPromotionId());
        $promotions = $service->listPromotions();
        $this->assertCount(0, $promotions);
    }

    /**
     * Test if BasketPromotionTarget is well setted
     *
     * @dataProvider basketPromotionTargetProvider
     */
    public function testPromotionsTarget(string $targetClass, ?array $args = null): void
    {
        if (\is_null($args) === true) {
            $target = new $targetClass();
        } else {
            $target = new $targetClass(...$args);
        }

        $service = $this->buildBasketPromotionService();

        // BasketPromotion is pushed in API then $savedPromotion is set with result of get query
        $savedPromotion = $service->savePromotion(
            $this->getASaveBasketPromotionCommand($target)
        );

        $this->assertInstanceOf(BasketPromotion::class, $savedPromotion);
        $this->assertInstanceOf($targetClass, $savedPromotion->getTarget());
    }

    public function basketPromotionTargetProvider(): array
    {
        return [
            [BasketTarget::class],
            [ProductsTarget::class, [1, 4, 7]],
            [ShippingTarget::class],
        ];
    }

    /**
     * @dataProvider badTargetProvider
     */
    public function testBadTargetResponseFormat($badTarget)
    {
        // API response
        $response = json_decode('{"promotion_id":"94e4819b-27fa-49b5-af62-fcf937935e5d","company_id":3,"name":"test promotion","active":true,"rule":{"type":"and","items":[{"type":"basket_price_superior_to","value":3.13},{"type":"basket_price_inferior_to","value":3.15},{"type":"or","items":[{"type":"basket_has_product_in_list","products_ids":[1,2,3]},{"type":"basket_has_product_in_list","products_ids":[4,5,7]}]},{"type":"max_usage_count","value":100},{"type":"max_usage_count_per_user","value":1}]},"period":{"from":"1992-09-07T00:00:00+00:00","to":"2019-01-01T00:00:00+00:00"},"discounts":[{"type":"percentage","percentage":2},{"type":"fixed","value":3.5}],"target":{"type":"product_in_basket;1,4,7"},"coupon":null}', true);
        // Set bad target format
        $response['target']['type'] = $badTarget;

        $this->expectException(\Exception::class);
        new BasketPromotion($response);
    }


    public function badTargetProvider(): array
    {
        return [
            [null],
            [18],
            [''],
            [';'],
            ['product_in_basket'],
            ['product_in_basket;'],
            ['bad_target_type'],
        ];
    }

    public function testPromotionWithoutRules(): void
    {
        $basketPromotionsService = $this->buildBasketPromotionService();

        $nbPromotions = \count($basketPromotionsService->listPromotions());

        $savedPromotion = $basketPromotionsService->savePromotion(
            SaveBasketPromotionCommand::createNew()
                ->setName('test promotion without rules')
                ->setActive(true)
                ->setDiscounts([new FixedDiscount(3.5)])
                ->setPeriod(new PromotionPeriod(new \DateTime('2010-01-01'), new \DateTime('2025-01-01')))
                ->setTarget(new BasketTarget())
        );

        static::assertNull($savedPromotion->getRule());
        static::assertCount(++$nbPromotions, $basketPromotionsService->listPromotions());
    }

    private function getASaveBasketPromotionCommand(?BasketPromotionTarget $target = null)
    {
        $from = new \DateTimeImmutable('1992-09-07T00:00:00+0000');
        $to = new \DateTime('@1546300800');

        return SaveBasketPromotionCommand::createNew()
            ->setName('test promotion')
            ->setActive(true)
            ->setDiscounts(
                [
                    new PercentageDiscount(2),
                    new FixedDiscount(3.5),
                ]
            )
            ->setPeriod(new PromotionPeriod($from, $to))
            ->setRule(
                new AndBasketRule(
                    new BasketPriceSuperiorToRule(3.13),
                    new BasketPriceInferiorToRule(3.15),
                    new OrBasketRule(
                        new BasketHasProductInListRule(1, 2, 3),
                        new BasketHasProductInListRule(4, 5, 7)
                    ),
                    new MaxUsageCountRule(100),
                    new MaxUsageCountPerUserRule(1)
                )
            )
            ->setTarget($target ?? new ProductsTarget(1, 4, 7));
    }

    public function testPromotionWithGroups(): void
    {
        $basketPromotionsService = $this->buildBasketPromotionService('vendor@wizaplace.com', static::VALID_PASSWORD);

        $savedPromotion = $basketPromotionsService->savePromotion(
            SaveBasketPromotionCommand::createNew()
                ->setName('test promotion without rules')
                ->setActive(true)
                ->setDiscounts([new FixedDiscount(3.5)])
                ->setPeriod(new PromotionPeriod(new \DateTime('2010-01-01'), new \DateTime('2025-01-01')))
                ->setTarget(new BasketTarget())
                ->setRule(
                    new AndBasketRule(
                        new BasketPriceSuperiorToRule(100),
                        new BasketHasGroupInListRule(['0c43c4e7-9c54-11eb-a9e9-0242ac120007'])
                    )
                )
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
        static::assertSame(['0c43c4e7-9c54-11eb-a9e9-0242ac120007'], $rule->getGroupsIds());
    }

    public function testUpdateBasketPromotionWithGroups(): void
    {
        $service = $this->buildBasketPromotionService('vendor@wizaplace.com', static::VALID_PASSWORD);

        $updatedPromotion = $service->savePromotion(
            SaveBasketPromotionCommand::updateExisting('93b6f076-3bc1-4c77-a2d0-350bdead9290')
                ->setRule(new BasketHasGroupInListRule(['0c43c4e7-9c54-11eb-a9e9-0242ac120008']))
        );

        static::assertInstanceOf(BasketPromotion::class, $updatedPromotion);
        $rootRule = $updatedPromotion->getRule();
        static::assertInstanceOf(BasketHasGroupInListRule::class, $rootRule);
        static::assertSame(['0c43c4e7-9c54-11eb-a9e9-0242ac120008'], $rootRule->getGroupsIds());
    }

    public function testGetBasketPromotionsWithGroups(): void
    {
        $service = $this->buildBasketPromotionService('vendor@wizaplace.com', static::VALID_PASSWORD);

        $promotionList = $service->listPromotions();

        $rootRule = $promotionList[0]->getRule();
        static::assertInstanceOf(BasketRule::class, $rootRule);
        static::assertContainsOnly(BasketRule::class, $rootRule->getItems());
        static::assertCount(2, $rootRule->getItems());
        $rule = $rootRule->getItems()[1];
        static::assertInstanceOf(BasketHasGroupInListRule::class, $rule);
        static::assertSame(['0c43c4e7-9c54-11eb-a9e9-0242ac120007'], $rule->getGroupsIds());
    }

    private function buildBasketPromotionService(string $email = 'vendor@world-company.com', string $password = 'password-vendor'): BasketPromotionService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, static::VALID_PASSWORD);

        return new BasketPromotionService($apiClient);
    }
}
