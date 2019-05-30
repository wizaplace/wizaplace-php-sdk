<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\Shipping;

use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Shipping\Shipping;
use Wizaplace\SDK\Shipping\ShippingRate;
use Wizaplace\SDK\Shipping\ShippingService;
use Wizaplace\SDK\Shipping\ShippingStatus;
use Wizaplace\SDK\Tests\ApiTestCase;

final class ShippingServiceTest extends ApiTestCase
{
    public function testGetAllShippings()
    {
        $shippingService = $this->buildShippingService();

        foreach ($shippingService->getAll() as $shipping) {
            $this->assertInstanceOf(Shipping::class, $shipping);
            $this->assertNotNull($shipping->getId());
            $this->assertNotNull($shipping->getName());
            $this->assertNotNull($shipping->getDeliveryTime());
            $this->assertNotNull($shipping->isEnabled());
            $this->assertNull($shipping->getRates());
            $this->assertNull($shipping->getDescription());
        }
    }

    public function testGetAShipping()
    {
        $shippingService = $this->buildShippingService();

        $shipping = $shippingService->getById(1);

        $this->assertInstanceOf(Shipping::class, $shipping);
        $this->assertNotNull($shipping->getId());
        $this->assertNotNull($shipping->getName());
        $this->assertNotNull($shipping->getDeliveryTime());
        $this->assertNotNull($shipping->isEnabled());
        $this->assertNotNull($shipping->getRates());
        foreach ($shipping->getRates() as $rate) {
            $this->assertInstanceOf(ShippingRate::class, $rate);
            $this->assertNotNull($rate->getAmount());
            $this->assertNotNull($rate->getValue());
        }
        $this->assertNotNull($shipping->getDescription());
    }

    public function testGetANotFoundShipping()
    {
        $shippingService = $this->buildShippingService();

        $this->expectException(NotFound::class);
        $shippingService->getById(123456789);
    }

    public function testPutAShipping()
    {
        $shippingService = $this->buildShippingService();

        $id = $shippingService->put(1, ShippingStatus::DISABLED(), [
            new ShippingRate([
                'amount' => 0,
                'value' => 10,
            ]),
            new ShippingRate([
                'amount' => 1,
                'value' => 10,
            ]),
        ]);
        $this->assertSame(1, $id);


        $shipping = $shippingService->getById(1);

        $this->assertInstanceOf(Shipping::class, $shipping);
        $this->assertNotNull($shipping->getId());
        $this->assertNotNull($shipping->getName());
        $this->assertNotNull($shipping->getDeliveryTime());
        $this->assertFalse($shipping->isEnabled());
        $this->assertNotNull($shipping->getRates());
        $this->assertInstanceOf(ShippingRate::class, $shipping->getRates()[0]);
        $this->assertInstanceOf(ShippingRate::class, $shipping->getRates()[1]);
        $this->assertSame(0, $shipping->getRates()[0]->getAmount());
        $this->assertSame(1, $shipping->getRates()[1]->getAmount());
        $this->assertEquals(10, $shipping->getRates()[0]->getValue());
        $this->assertEquals(10, $shipping->getRates()[1]->getValue());
        $this->assertNotNull($shipping->getDescription());
    }

    private function buildShippingService($userEmail = 'vendor@wizaplace.com', $userPassword = 'password'): ShippingService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($userEmail, $userPassword);

        return new ShippingService($apiClient);
    }
}
