<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\Shipping;

use Wizaplace\SDK\Shipping\MondialRelayService;
use Wizaplace\SDK\Tests\ApiTestCase;

final class MondialRelayServiceTest extends ApiTestCase
{
    public function testGetBrandCode()
    {
        $mondialRelay = $this->buildMondialRelayService();

        $brandCode = $mondialRelay->getBrandCode();

        $this->assertSame($brandCode->getValue(), 'BDTEST13');
    }

    private function buildMondialRelayService($userEmail = 'admin@wizaplace.com', $userPassword = 'password'): MondialRelayService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($userEmail, $userPassword);

        return new MondialRelayService($apiClient);
    }
}
