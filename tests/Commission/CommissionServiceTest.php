<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Commission;

use Wizaplace\SDK\Commission\Commission;
use Wizaplace\SDK\Commission\CommissionService;
use Wizaplace\SDK\Tests\ApiTestCase;

class CommissionServiceTest extends ApiTestCase
{
    /** @var CommissionService */
    protected $commissionService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commissionService = $this->buildCommissionService();
        $this->deleteDefaultCommission();
    }

    public function testAddMarketplaceCommission(): void
    {
        $this->commissionService->addMarketplaceCommission(new Commission(
            [
                'percent' => 2.50,
                'fixed' => 0.50,
                'maximum' => 10,
            ]
        ));
        $defaultCommission = $this->commissionService->getMarketplaceCommission();

        static::assertSame(2.50, $defaultCommission->getPercentAmount());
        static::assertSame(0.50, $defaultCommission->getFixedAmount());
        static::assertSame(10.0, $defaultCommission->getMaximumAmount());
    }

    public function testAddCategoryCommission(): void
    {
        $commissionId = $this->commissionService->addCategoryCommission(new Commission(
            [
                'category' => 5,
                'percent' => 2.50,
                'fixed' => 0.50,
                'maximum' => 10,
            ]
        ));
        $addedCommission = $this->commissionService->getCommission($commissionId);

        static::assertSame(5, $addedCommission->getCategoryId());
        static::assertSame(0, $addedCommission->getCompanyId());
        static::assertSame(2.50, $addedCommission->getPercentAmount());
        static::assertSame(0.0, $addedCommission->getFixedAmount());
        static::assertSame(10.0, $addedCommission->getMaximumAmount());
    }

    public function testAddCompanyCommission(): void
    {
        $commissionId = $this->commissionService->addCompanyCommission(new Commission(
            [
                'company' => 2,
                'category' => 5,
                'percent' => 2.50,
                'fixed' => 0.50,
                'maximum' => 10,
            ]
        ));
        $addedCommission = $this->commissionService->getCommission($commissionId);

        static::assertSame(2, $addedCommission->getCompanyId());
        static::assertSame(5, $addedCommission->getCategoryId());
        static::assertSame(2.50, $addedCommission->getPercentAmount());
        static::assertSame(0.0, $addedCommission->getFixedAmount());
        static::assertSame(10.0, $addedCommission->getMaximumAmount());
    }

    public function testUpdateMarketplaceCommission(): void
    {
        $commissionId = $this->commissionService->addMarketplaceCommission(new Commission(
            [
                'percent' => 2.50,
                'fixed' => 0.50,
                'maximum' => 10,
            ]
        ));
        $this->commissionService->updateMarketplaceCommission(new Commission(
            [
                'id' => $commissionId,
                'percent' => 3.50,
                'fixed' => 1.50,
                'maximum' => 20,
            ]
        ));
        $defaultCommission = $this->commissionService->getMarketplaceCommission();

        static::assertSame(3.50, $defaultCommission->getPercentAmount());
        static::assertSame(1.50, $defaultCommission->getFixedAmount());
        static::assertSame(20.0, $defaultCommission->getMaximumAmount());
    }

    public function testUpdateCategoryCommission(): void
    {
        $commissionId = $this->commissionService->addCategoryCommission(new Commission(
            [
                'category' => 8,
                'percent' => 2.50,
                'fixed' => 0.50,
                'maximum' => 10,
            ]
        ));
        $this->commissionService->updateCategoryCommission(new Commission(
            [
                'id' => $commissionId,
                'category' => 8,
                'percent' => 3.50,
                'fixed' => 1.50,
                'maximum' => 20,
            ]
        ));
        $commission = $this->commissionService->getCommission($commissionId);

        static::assertSame(0, $commission->getCompanyId());
        static::assertSame(8, $commission->getCategoryId());
        static::assertSame(3.50, $commission->getPercentAmount());
        static::assertSame(0.0, $commission->getFixedAmount());
        static::assertSame(20.0, $commission->getMaximumAmount());
    }

    public function testUpdateCompanyCommission(): void
    {
        $commissionId = $this->commissionService->addCompanyCommission(new Commission(
            [
                'company' => 3,
                'category' => 5,
                'percent' => 2.50,
                'fixed' => 0.50,
                'maximum' => 10,
            ]
        ));
        $this->commissionService->updateCompanyCommission(new Commission(
            [
                'id' => $commissionId,
                'company' => 3,
                'category' => 5,
                'percent' => 3.50,
                'fixed' => 1.50,
                'maximum' => 20,

            ]
        ));
        $commission = $this->commissionService->getCommission($commissionId);

        static::assertSame(3, $commission->getCompanyId());
        static::assertSame(5, $commission->getCategoryId());
        static::assertSame(3.50, $commission->getPercentAmount());
        static::assertSame(0.0, $commission->getFixedAmount());
        static::assertSame(20.0, $commission->getMaximumAmount());
    }

    public function testGetCommissions(): void
    {
        $marketplaceCommissionId = $this->commissionService->addMarketplaceCommission(new Commission(
            [
                'percent' => 2.50,
                'fixed' => 0.50,
                'maximum' => 10,
            ]
        ));
        $companyCommissionId = $this->commissionService->addCompanyCommission(new Commission(
            [
                'company' => 3,
                'category' => 9,
                'percent' => 2.50,
                'fixed' => 0.50,
                'maximum' => 10,
            ]
        ));
        $commissions = $this->commissionService->getCommissions();
        $marketplaceCommission = $this->commissionService->getCommission($marketplaceCommissionId);
        $companyCommission = $this->commissionService->getCommission($companyCommissionId);

        static::assertTrue(\in_array($marketplaceCommission, $commissions));
        static::assertTrue(\in_array($companyCommission, $commissions));
    }

    public function testGetCommission(): void
    {
        $commissionId = $this->commissionService->addMarketplaceCommission(new Commission(
            [
                'percent' => 2.50,
                'fixed' => 0.50,
                'maximum' => 10,
            ]
        ));
        $commission = $this->commissionService->getCommission($commissionId);

        static::assertSame(2.50, $commission->getPercentAmount());
        static::assertSame(0.50, $commission->getFixedAmount());
        static::assertSame(10.0, $commission->getMaximumAmount());
    }

    public function testDeleteCommission(): void
    {
        $this->commissionService->addMarketplaceCommission(new Commission(
            [
                'percent' => 2.50,
                'fixed' => 0.50,
                'maximum' => 10,
            ]
        ));
        $this->commissionService->deleteCommission($this->commissionService->getMarketplaceCommission()->getId());
        $defaultCommission = $this->commissionService->getMarketplaceCommission();

        static::assertSame(0.0, $defaultCommission->getPercentAmount());
        static::assertSame(0.0, $defaultCommission->getFixedAmount());
        static::assertNull($defaultCommission->getMaximumAmount());
    }

    private function deleteDefaultCommission(): void
    {
        $defaultCommission = $this->commissionService->getMarketplaceCommission();

        if (\strlen($defaultCommission->getId()) > 0) {
            $this->commissionService->deleteCommission($defaultCommission->getId());
        }
    }

    private function buildCommissionService($userEmail = 'admin@wizaplace.com', $userPassword = 'password'): CommissionService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($userEmail, $userPassword);

        return new CommissionService($apiClient);
    }
}
