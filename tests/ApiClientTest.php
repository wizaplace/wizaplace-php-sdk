<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Jean85\PrettyVersions;
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

    public function testAuthenticationInvalidApiToken()
    {
        $apiClient = $this->buildApiClient();
        $orderService = new OrderService($apiClient);

        //user with empty api_key
        $apiKey = $apiClient->authenticate('test-client@wiza.com', 'password');
        static::assertNotNull($apiKey);

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
        $version = PrettyVersions::getVersion('wizaplace/sdk')->getPrettyVersion();
        $this->assertNotEmpty($version);

        $expectedRequestOptions = [
            'headers' => [
                'User-Agent' => 'Wizaplace-PHP-SDK/' . $version,
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

    public function testOAuthAuthentication()
    {
        $apiClient = $this->buildApiClient();
        $orderService = new OrderService($apiClient);

        $apiKey = $apiClient->oauthAuthenticate('4/AAAfMc0UWDe1LOXv93D0ZKM2f5VuazwpHl004ZPfjB1tpBQDIa95uYFPPPMcH2ePnJoPhgzcp6ZWGDbEGYS9-6Q#');
        $this->assertNotNull($apiKey);

        // Test an authenticated call.
        // If the authentication did not "register" properly, we will get an exception and the test will fail.
        $orderService->getOrders();
    }

    public function testBadCredentialsOAuthAuthentication()
    {
        $apiClient = $this->buildApiClient();

        $this->expectException(BadCredentials::class);
        $apiClient->oauthAuthenticate('foo');
    }

    public function testGetOAuthAuthorizationUrl()
    {
        $url = $this->buildApiClient()->getOAuthAuthorizationUrl();

        $this->assertSame('https://accounts.google.com/o/oauth2/auth?response_type=code&access_type=online&client_id=cli3nt_1d&redirect_uri=http%3A%2F%2Fwizaplace.fr%2Foauth&state&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email&approval_prompt=auto', $url);
    }
}
