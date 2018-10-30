<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\Shipping;

use Wizaplace\SDK\Shipping\MondialRelayOpening;
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

    public function testGetPickupPoint()
    {
        $mondialRelay = $this->buildMondialRelayService();

        $point = $mondialRelay->getPickupPoint('003393');

        $this->assertSame($point->getId(), '003393');
        $this->assertSame($point->getAddress(), [
            'KFE TOLSTOI PMU',
            '',
            '97 COURS TOLSTOI',
            '',
        ]);
        $this->assertSame($point->getZipCode(), '69100');
        $this->assertSame($point->getCity(), 'VILLEURBANNE');
        $this->assertSame($point->getCountry(), 'FR');
        $this->assertSame($point->getLocation1(), '');
        $this->assertSame($point->getLocation2(), '');
        $this->assertSame($point->getLatitude(), '45,7622380');
        $this->assertSame($point->getLongitude(), '04,8804019');
        $this->assertSame($point->getActivityType(), '000');
        $this->assertSame($point->getInformation(), '');
        $this->assertSame($point->getAvailabilityInformation(), null);
        $this->assertSame($point->getUrlPicture(), 'https://ww2.mondialrelay.com/public/permanent/photo_relais.aspx?ens=CC______41&num=003393&pays=FR&crc=579966753D9EE050A340DCDC50842C3D');
        $this->assertSame($point->getUrlMap(), 'https://ww2.mondialrelay.com/public/permanent/plan_relais.aspx?ens=BDTEST1311&num=003393&pays=FR&crc=30ED83CB00D7DC4EFC5B3281E987FE58');
        $this->assertSame($point->getDistance(), 0);

        for ($i = 0; $i <= 6; $i++) {
            $opening = $point->getOpeningHours()[$i];
            $this->assertInstanceOf(MondialRelayOpening::class, $opening);
            $this->assertSame($opening->getDay(), $i);
        }
    }

    private function buildMondialRelayService($userEmail = 'admin@wizaplace.com', $userPassword = 'password'): MondialRelayService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($userEmail, $userPassword);

        return new MondialRelayService($apiClient);
    }
}
