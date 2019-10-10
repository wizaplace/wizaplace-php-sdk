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

    public function testAddCompanyCommission(): void
    {
        $commission = new Commission(
            [
                'company' => 2,
                'category' => 5,
                'percent' => 2.50,
                'fixed' => 0.50,
                'maximum' => 10,
            ]
        );
        $commissionId = $this->commissionService->addCompanyCommission($commission);
        $addedCommission = $this->commissionService->getCommission($commissionId);

        static::assertSame(5, $addedCommission->getCategoryId());
        static::assertSame(2, $addedCommission->getCompanyId());
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
