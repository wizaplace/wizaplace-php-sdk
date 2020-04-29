<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

namespace Wizaplace\SDK\Tests\Division;

use Wizaplace\SDK\Division\DivisionService;
use Wizaplace\SDK\Division\DivisionSettings;
use Wizaplace\SDK\Tests\ApiTestCase;

class DivisionServiceTest extends ApiTestCase
{
    public function testGetDivisionsSettings(): void
    {
        $divisionService = $this->buildDivisionService();
        $divisionsSettings = $divisionService->getDivisionsSettings();

        static::assertEquals(['FR'], $divisionsSettings->getIncluded());
        static::assertEquals(['FR-59', 'FR-IDF'], $divisionsSettings->getExcluded());
    }

    public function testPatchDivisionsSettings(): void
    {
        $divisionService = $this->buildDivisionService();
        $data = [
            'included' => ['FR'],
            'excluded' => ['FR-ARA', 'FR-NC']
        ];
        $divisionsSettings = new DivisionSettings($data);
        $divisionService->patchDivisionsSettings($divisionsSettings);

        static::assertEquals($data['included'], $divisionService->getDivisionsSettings()->getIncluded());
        static::assertEquals($data['excluded'], $divisionService->getDivisionsSettings()->getExcluded());
    }

    private function buildDivisionService(
        string $email = 'admin@wizaplace.com',
        string $password = 'password'
    ): DivisionService {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new DivisionService($apiClient);
    }
}
