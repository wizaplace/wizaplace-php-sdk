<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Wizaplace\SDK\ApiClient;
use Wizaplace\SDK\Authentication\BadCredentials;
use Wizaplace\SDK\Order\OrderService;

final class ApiClientTest extends ApiTestCase
{
    public function testAuthentication()
    {
        $apiClient = $this->buildApiClient();
        $orderService = new OrderService($apiClient);

        $apiKey = $apiClient->authenticate('customer-1@world-company.com', 'password-customer-1');
        $this->assertNotNull($apiKey);

        // Test an authenticated call.
        // If the authentication did not "register" properly, we will get an exception and the test will fail.
        $orderService->getOrders();
    }

    public function testBadCredentialsAuthentication()
    {
        $apiClient = $this->buildApiClient();

        $this->expectException(BadCredentials::class);
        $apiClient->authenticate("admin@wizaplace.com", "wrongPassword");
    }

    public function testUserAgentContainsVersion()
    {
        $expectedRequestOptions = [
            'headers' => [
                'User-Agent' => 'Wizaplace-PHP-SDK/9999999-dev',
            ],
        ];

        /** @var Client|\PHPUnit_Framework_MockObject_MockObject $guzzleMock */
        $guzzleMock = $this->createMock(Client::class);
        $guzzleMock
            ->expects($this->exactly(5))
            ->method('request')
            ->withConsecutive(
                ['GET', 'test', $expectedRequestOptions],
                ['POST', 'test', $expectedRequestOptions],
                ['PUT', 'test', $expectedRequestOptions],
                ['DELETE', 'test', $expectedRequestOptions],
                ['PATCH', 'test', $expectedRequestOptions]
            )
            ->willReturn(new Response())
        ;

        $apiClient = new ApiClient($guzzleMock);

        $apiClient->get('test');
        $apiClient->post('test');
        $apiClient->put('test');
        $apiClient->delete('test');
        $apiClient->rawRequest('PATCH', 'test');
    }
}
