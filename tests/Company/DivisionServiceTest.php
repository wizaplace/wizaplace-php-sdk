<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Company;

use Wizaplace\SDK\Company\DivisionService;
use Wizaplace\SDK\Division\DivisionSettings;
use Wizaplace\SDK\Tests\ApiTestCase;

class DivisionServiceTest extends ApiTestCase
{
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

    private function buildDivisionService(
        string $email = 'vendor@world-company.com',
        string $password = 'password-vendor'
    ): DivisionService {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new DivisionService($apiClient);
    }
}
