<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\Catalog;

use Wizaplace\Catalog\CatalogService;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class CatalogServiceTest extends TestCase
{
    /**
     * Simple minimal test. @TODO : decide of a testing strategy
     */
    public function testInstantiation()
    {
        $catalogService = new CatalogService('http://wizaplace.test/', new Client());
        $this->assertNotNull($catalogService); // dummy assertion, because PHPUnit complains if there are none
    }
}
