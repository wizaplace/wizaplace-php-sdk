<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests;

/**
 * This is just here for example usages.
 * Having them as runnable tests ensures that they are up to date.
 */
class ExampleTest extends ApiTestCase
{
    /**
     * Shows how to make a simple product search without any filters.
     * @test
     */
    public function basicUsage()
    {
        $marketplaceApiUri = 'http://wizaplace.loc/api/v1/'; // replace that value with your own
        $httpClient = new \GuzzleHttp\Client([
            'base_uri' => $marketplaceApiUri,
        ]);
        $wizaplaceClient = new \Wizaplace\ApiClient($httpClient);
        $catalogService = new \Wizaplace\Catalog\CatalogService($wizaplaceClient);
        $products = $catalogService->search();

        // Just here so PHPUnit does not complain about the lack of assertions
        $this->assertNotEmpty($products);
    }
}
