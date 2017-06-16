<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

namespace Wizaplace\tests\Cms;

use Wizaplace\Cms\BannerService;
use Wizaplace\Tests\ApiTestCase;

class BannerServiceTest extends ApiTestCase
{
    public function testGetHomepageBanners()
    {
        $cmsService = new BannerService($this->getGuzzleClient());
        $banners = $cmsService->getHomepageBanners();

        $this->assertNotEmpty($banners);
    }
}
