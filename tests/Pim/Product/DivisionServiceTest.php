<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Pim\Product;

use Wizaplace\SDK\Division\DivisionSettings;
use Wizaplace\SDK\Division\DivisionsTreeFilters;
use Wizaplace\SDK\Pim\Product\DivisionService;
use Wizaplace\SDK\Tests\ApiTestCase;
use Wizaplace\SDK\Tests\Division\DivisionsTreeTrait;

class DivisionServiceTest extends ApiTestCase
{
    use DivisionsTreeTrait;

    /** @var int productId used in most cases */
    private $productId = 1;

    public function testGetProductDivisionsSettings(): void
    {
        $divisionService = $this->buildDivisionService();
        $divisionsSettings = $divisionService->getDivisionsSettings($this->productId);

        static::assertEquals(['FR'], $divisionsSettings->getIncluded());
        static::assertEquals(['FR-59', 'FR-IDF'], $divisionsSettings->getExcluded());
    }

    public function testPatchProductDivisionsSettings(): void
    {
        $divisionService = $this->buildDivisionService();
        $data = [
            'included' => ['FR'],
            'excluded' => ['FR-ARA', 'FR-NC']
        ];
        $divisionsSettings = new DivisionSettings($data);
        $divisionService->patchDivisionsSettings($this->productId, $divisionsSettings);

        static::assertEquals($data['included'], $divisionService->getDivisionsSettings($this->productId)->getIncluded());
        static::assertEquals($data['excluded'], $divisionService->getDivisionsSettings($this->productId)->getExcluded());
    }

    public function testProductDivisionsTree(): void
    {
        $divisionService = $this->buildDivisionService();

        $this->assertDivisionTree(
            $divisionService->getDivisionsTree(
                $this->productId
            )
        );
    }

    public function testProductDivisionsTreeFiltersEnabled(): void
    {
        $divisionService = $this->buildDivisionService();

        $this->assertDivisionTreeFiltersEnabled(
            $divisionService->getDivisionsTree(
                $this->productId,
                (new DivisionsTreeFilters())->setIsEnabled(true)
            ),
            true
        );
    }

    public function testProductDivisionsTreeFiltersRootCode()
    {
        $divisionService = $this->buildDivisionService();

        $this->assertDivisionTreeFiltersRootCode(
            $divisionService->getDivisionsTree(
                $this->productId,
                (new DivisionsTreeFilters())->setRootCode('FR-IDF')
            ),
            'FR-IDF'
        );
    }

    private function buildDivisionService(
        string $email = 'vendor@world-company.com',
        string $password = 'password-vendor'
    ): DivisionService {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new DivisionService($apiClient);
    }
}
