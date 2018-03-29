<?php
/**
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Pim\Tax;

use Wizaplace\SDK\Pim\Tax\Tax;
use Wizaplace\SDK\Pim\Tax\TaxService;
use Wizaplace\SDK\Tests\ApiTestCase;

class TaxServiceTest extends ApiTestCase
{
    public function testGetTaxes()
    {
        /** @var Tax[] $taxes */
        $taxes = $this->buildTaxService()->getTaxes();

        $this->assertNotEmpty($taxes);
        $this->assertContainsOnly(Tax::class, $taxes);
        $this->assertNotNull($taxes[0]->getId());
        $this->assertNotNull($taxes[0]->getName());
    }

    private function buildTaxService($userEmail = 'vendor@wizaplace.com', $userPassword = 'password'): TaxService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($userEmail, $userPassword);

        return new TaxService($apiClient);
    }
}
