<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests;

use Wizaplace\SDK\AuthLog\AuthLogService;

final class AuthLogServiceTest extends ApiTestCase
{
    public function testGet(): void
    {
        $authLogService = $this->buildAuthLogService();
        $authLog = $authLogService->get(1);

        $expected = [
            'id' => 1,
            'login' => 'hello@world.fr',
            'status' => 'SUCCESS',
            'source' => 'SSO',
            'destination' => 'FRONT',
        ];

        $reality = [
            'id' => $authLog['id'],
            'login' => $authLog['login'],
            'status' => $authLog['status'],
            'source' => $authLog['source'],
            'destination' => $authLog['destination'],
        ];

        $this->assertEquals($expected, $reality);
    }

    public function testSearch(): void
    {
        $authLogService = $this->buildAuthLogService();
        $authLogs = $authLogService->search([
            'status' => ['SUCCESS'],
            'sort_order' => 'asc',
        ]);
        $this->assertEquals(19, $authLogs['total']);
    }

    private function buildAuthLogService(
        string $email = 'admin@wizaplace.com',
        string $password = 'password'
    ): AuthLogService {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new AuthLogService($apiClient);
    }
}
