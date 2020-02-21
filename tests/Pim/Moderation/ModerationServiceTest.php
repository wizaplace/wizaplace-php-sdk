<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Pim\Category;

use Wizaplace\SDK\Exception\UnauthorizedModerationAction;
use Wizaplace\SDK\Pim\Moderation\ModerationService;
use Wizaplace\SDK\Pim\Product\ProductApprovalStatus;
use Wizaplace\SDK\Pim\Product\ProductService;
use Wizaplace\SDK\Pim\Product\ProductSummary;
use Wizaplace\SDK\Tests\ApiTestCase;

class ModerationServiceTest extends ApiTestCase
{
    /**
     * @var ModerationService
     */
    protected $moderationService;
    /**
     * @var ProductService
     */
    protected $productService;

    protected function setUp(): void
    {
        parent::setUp();

        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('admin@wizaplace.com', 'password');

        $this->moderationService = new ModerationService($apiClient);
        $this->productService = new ProductService($apiClient);
    }

    /**
     * Test moderation of all products of a company
     */
    public function testModerationByCompany(): void
    {
        // Initial state
        $productSummaryArray = $this->getProductsSummary();

        static::assertEquals(11, $productSummaryArray[3][ProductApprovalStatus::APPROVED()->getKey()]);
        static::assertEquals(2, $productSummaryArray[3][ProductApprovalStatus::PENDING()->getKey()]);
        static::assertEquals(1, $productSummaryArray[3][ProductApprovalStatus::REJECTED()->getKey()]);

        static::assertEquals(1, $productSummaryArray[4][ProductApprovalStatus::APPROVED()->getKey()]);
        static::assertEquals(1, $productSummaryArray[4][ProductApprovalStatus::PENDING()->getKey()]);

        // We approve all products of company 3
        $this->moderationService->moderateByCompany(3, 'approve');

        // Pending products of company 3 should now be approved
        $productSummaryArray = $this->getProductsSummary();

        static::assertEquals(13, $productSummaryArray[3][ProductApprovalStatus::APPROVED()->getKey()]);
        static::assertArrayNotHasKey(ProductApprovalStatus::PENDING()->getKey(), $productSummaryArray[3]);
        static::assertEquals(1, $productSummaryArray[3][ProductApprovalStatus::REJECTED()->getKey()]);

        static::assertEquals(1, $productSummaryArray[4][ProductApprovalStatus::APPROVED()->getKey()]);
        static::assertEquals(1, $productSummaryArray[4][ProductApprovalStatus::PENDING()->getKey()]);
    }

    /**
     * Test moderation of one product
     */
    public function testModerationByProduct(): void
    {
        $productSummaryArray = $this->getProductsSummary();
        static::assertEquals(1, $productSummaryArray[4][ProductApprovalStatus::PENDING()->getKey()]);

        $this->moderationService->moderateByProduct(17, 'approve');
        $productSummaryArray = $this->getProductsSummary();
        static::assertArrayNotHasKey(ProductApprovalStatus::PENDING()->getKey(), $productSummaryArray[4]);
    }

    /**
     * Test company products moderation with unknown state
     */
    public function testModerationByCompanyException(): void
    {
        static::expectException(UnauthorizedModerationAction::class);
        $this->moderationService->moderateByCompany(1, 'wrong');
    }

    /**
     * Test products moderation with unknown state
     */
    public function testModerationByProductException(): void
    {
        static::expectException(UnauthorizedModerationAction::class);
        $this->moderationService->moderateByProduct(1, 'wrong');
    }

    /**
     * Count how many products we have for each company and each approval state
     * @return int[][]
     */
    private function getProductsSummary(): array
    {
        $productsToApprove = $this->productService->listProducts()->getProducts();
        $productSummaryArray = [];

        // Summary array filling
        array_map(
            function (ProductSummary $product) use (&$productSummaryArray): void {
                $companyID = $product->getCompanyId();
                $statusKey = $product->getApprovalStatus()->getKey();

                $productSummaryArray[$companyID] = $productSummaryArray[$companyID] ?? [];

                $productSummaryArray[$companyID][$statusKey]
                    = true === \array_key_exists($statusKey, $productSummaryArray[$companyID])
                        ? $productSummaryArray[$companyID][$statusKey] + 1
                        : 1;
            },
            $productsToApprove
        );

        return $productSummaryArray;
    }
}
