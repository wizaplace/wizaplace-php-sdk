<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Vendor\Promotion;

use Wizaplace\SDK\Exception\PromotionNotFound;
use Wizaplace\SDK\Tests\ApiTestCase;
use Wizaplace\SDK\Vendor\Promotion\CatalogPromotion;
use Wizaplace\SDK\Vendor\Promotion\CatalogPromotionService;
use Wizaplace\SDK\Vendor\Promotion\Discounts\Discount;
use Wizaplace\SDK\Vendor\Promotion\Discounts\FixedDiscount;
use Wizaplace\SDK\Vendor\Promotion\Discounts\PercentageDiscount;
use Wizaplace\SDK\Vendor\Promotion\PromotionPeriod;
use Wizaplace\SDK\Vendor\Promotion\Rules\AndCatalogRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\CatalogRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\OrCatalogRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\ProductInCategoryListRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\ProductInListRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\ProductPriceInferiorToRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\ProductPriceSuperiorToRule;
use Wizaplace\SDK\Vendor\Promotion\SaveCatalogPromotionCommand;

final class PromotionServiceTest extends ApiTestCase
{
    public function testPromotionLifeCycle(): void
    {
        $service = $this->buildCatalogPromotionService();

        $promotions = $service->listPromotions();
        $this->assertCount(0, $promotions);

        $from = new \DateTimeImmutable('1992-09-07');
        $to = new \DateTime('@1546300800');
        $savedPromotion = $service->savePromotion(
            SaveCatalogPromotionCommand::createNew()
                ->setName('test promotion')
                ->setActive(true)
                ->setDiscounts([
                    new PercentageDiscount(2),
                    new FixedDiscount(3.5),
                ])
                ->setPeriod(new PromotionPeriod($from, $to))
                ->setRule(
                    new AndCatalogRule(
                        new ProductPriceSuperiorToRule(3.13),
                        new ProductPriceInferiorToRule(3.15),
                        new OrCatalogRule(
                            new ProductInListRule(1, 2, 3),
                            new ProductInCategoryListRule(4, 5, 6)
                        )
                    )
                )
        );

        // We check that the promotion and company IDs were set.
        $this->assertInternalType('string', $savedPromotion->getPromotionId());
        $this->assertNotEmpty($savedPromotion->getPromotionId());
        $this->assertInternalType('int', $savedPromotion->getCompanyId());
        $this->assertGreaterThan(0, $savedPromotion->getCompanyId());
        // We check that the promotion we got back matches what we wanted to save.
        $this->assertInstanceOf(CatalogPromotion::class, $savedPromotion);
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
        /** @var AndCatalogRule $rootRule */
        $rootRule = $savedPromotion->getRule();
        $this->assertInstanceOf(CatalogRule::class, $rootRule);
        $this->assertInstanceOf(AndCatalogRule::class, $rootRule);
        $this->assertContainsOnly(CatalogRule::class, $rootRule->getItems());
        $this->assertCount(3, $rootRule->getItems());
        /** @var ProductPriceSuperiorToRule $productPriceSuperior */
        $productPriceSuperior = $rootRule->getItems()[0];
        $this->assertInstanceOf(ProductPriceSuperiorToRule::class, $productPriceSuperior);
        $this->assertSame(3.13, $productPriceSuperior->getValue());
        /** @var ProductPriceInferiorToRule $productPriceInferior */
        $productPriceInferior = $rootRule->getItems()[1];
        $this->assertInstanceOf(ProductPriceInferiorToRule::class, $productPriceInferior);
        $this->assertSame(3.15, $productPriceInferior->getValue());
        /** @var OrCatalogRule $or */
        $or = $rootRule->getItems()[2];
        $this->assertInstanceOf(OrCatalogRule::class, $or);
        $this->assertContainsOnly(CatalogRule::class, $or->getItems());
        $this->assertCount(2, $or->getItems());
        /** @var ProductInListRule $productInList */
        $productInList = $or->getItems()[0];
        $this->assertInstanceOf(ProductInListRule::class, $productInList);
        $this->assertSame([1, 2, 3], $productInList->getProductsIds());
        /** @var ProductInCategoryListRule $productInCategoryList */
        $productInCategoryList = $or->getItems()[1];
        $this->assertInstanceOf(ProductInCategoryListRule::class, $productInCategoryList);
        $this->assertSame([4, 5, 6], $productInCategoryList->getCategoriesIds());
        $jsonSerialization = json_encode($savedPromotion);
        $this->assertGreaterThan(2, strlen($jsonSerialization));

        $actualPromotion = $service->getPromotion($savedPromotion->getPromotionId());
        $this->assertEquals($savedPromotion, $actualPromotion);

        // Let's update a few fields, but not all of them
        $updatedPromotion = $service->savePromotion(
            SaveCatalogPromotionCommand::updateExisting($savedPromotion->getPromotionId())
                ->setName('test promotion updated')
                ->setActive(false)
        );

        $this->assertSame($savedPromotion->getPromotionId(), $updatedPromotion->getPromotionId());
        $this->assertInstanceOf(CatalogPromotion::class, $updatedPromotion);
        $this->assertSame('test promotion updated', $updatedPromotion->getName());
        $this->assertSame(false, $updatedPromotion->isActive());

        $actualPromotion = $service->getPromotion($savedPromotion->getPromotionId());
        $this->assertEquals($updatedPromotion, $actualPromotion);

        $promotions = $service->listPromotions();
        $this->assertCount(1, $promotions);
        $this->assertContainsOnly(CatalogPromotion::class, $promotions);
        $this->assertEquals($actualPromotion, $promotions[0]);

        $service->deletePromotion($actualPromotion->getPromotionId());

        $promotions = $service->listPromotions();
        $this->assertCount(0, $promotions);

        $this->expectException(PromotionNotFound::class);
        $service->getPromotion($savedPromotion->getPromotionId());
    }

    private function buildCatalogPromotionService(string $email = 'vendor@world-company.com', string $password = 'password-vendor'): CatalogPromotionService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new CatalogPromotionService($apiClient);
    }
}
