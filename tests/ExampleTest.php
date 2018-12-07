<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests;

/**
 * This is just here for example usages.
 * Having them as runnable tests ensures that they are up to date.
 */
final class ExampleTest extends ApiTestCase
{
    /**
     * Shows how to make a simple product search without any filters.
     * @test
     */
    public function basicUsage()
    {
        $httpClient = new \GuzzleHttp\Client([
            'base_uri' => $this->getApiBaseUrl(),
        ]);
        $wizaplaceClient = new \Wizaplace\SDK\ApiClient($httpClient);
        $catalogService = new \Wizaplace\SDK\Catalog\CatalogService($wizaplaceClient);

        // Search
        $products = $catalogService->search();

        // Just here so PHPUnit does not complain about the lack of assertions
        $this->assertNotEmpty($products);
    }

    /**
     * Shows how to use a service which requires authentication.
     * @test
     */
    public function authenticationUsage()
    {
        $httpClient = new \GuzzleHttp\Client([
            'base_uri' => $this->getApiBaseUrl(),
        ]);
        $wizaplaceClient = new \Wizaplace\SDK\ApiClient($httpClient);
        $orderService = new \Wizaplace\SDK\Order\OrderService($wizaplaceClient);

        // Authentication
        $wizaplaceClient->authenticate('customer-3@world-company.com', 'password-customer-3');

        // Authenticated Action
        $orders = $orderService->getOrders();

        // Just here so PHPUnit does not complain about the lack of assertions
        $this->assertEmpty($orders);
    }
}
