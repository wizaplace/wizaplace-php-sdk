<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Company;

use Wizaplace\SDK\Company\DivisionService;
use Wizaplace\SDK\Division\Division;
use Wizaplace\SDK\Division\DivisionSettings;
use Wizaplace\SDK\Division\DivisionsTreeFilters;
use Wizaplace\SDK\Tests\ApiTestCase;
use Wizaplace\SDK\Tests\Division\DivisionsTreeTrait;

class DivisionServiceTest extends ApiTestCase
{
    use DivisionsTreeTrait;

    /** @var int companyId used in most cases */
    private $companyId = 3;

    public function testGetCompanyDivisionsSettings(): void
    {
        $divisionService = $this->buildDivisionService();
        $divisionsSettings = $divisionService->getDivisionsSettings(3);

        static::assertEquals(['FR'], $divisionsSettings->getIncluded());
        static::assertEquals(['FR-59', 'FR-IDF'], $divisionsSettings->getExcluded());
    }

    public function testPatchCompanyDivisionsSettings(): void
    {
        $divisionService = $this->buildDivisionService();
        $data = [
            'included' => ['FR'],
            'excluded' => ['FR-ARA', 'FR-NC']
        ];
        $divisionsSettings = new DivisionSettings($data);
        $divisionService->patchDivisionsSettings(3, $divisionsSettings);

        static::assertEquals($data['included'], $divisionService->getDivisionsSettings(3)->getIncluded());
        static::assertEquals($data['excluded'], $divisionService->getDivisionsSettings(3)->getExcluded());
    }

    public function testCompanyDivisionsTree(): void
    {
        $divisionService = $this->buildDivisionService();
        $this->assertDivisionTree($divisionService->getDivisionsTree(3));
    }

    public function testCompanyDivisionsTreeFiltersEnabled(): void
    {
        $divisionService = $this->buildDivisionService();

        $this->assertDivisionTreeFiltersEnabled(
            $divisionService->getDivisionsTree(
                3,
                (new DivisionsTreeFilters())->setIsEnabled(true)
            ),
            true
        );
    }

    public function testCompanyDivisionsTreeFiltersRootCode()
    {
        $divisionService = $this->buildDivisionService();

        $this->assertDivisionTreeFiltersRootCode(
            $divisionService->getDivisionsTree(
                3,
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
