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
    /** @test */
    public function basicUsage()
    {
        $httpClient = new \GuzzleHttp\Client([
            'base_uri' => 'https://wizacha.com/api/v1/',
        ]);
        $wizaplaceClient = new \Wizaplace\ApiClient($httpClient);
        $catalogService = new \Wizaplace\Catalog\CatalogService($wizaplaceClient);
        $products = $catalogService->search();
        $this->assertNotEmpty($products);
    }
}
