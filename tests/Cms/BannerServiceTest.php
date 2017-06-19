<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\Cms;

use Wizaplace\Cms\BannerService;
use Wizaplace\Tests\ApiTestCase;

class BannerServiceTest extends ApiTestCase
{
    public function testGetHomepageBanners()
    {
        $cmsService = new BannerService($this->getGuzzleClient());
        $banners = $cmsService->getHomepageBanners();

        $this->assertEquals(2, count($banners));
        $this->assertEquals('http://url-de-test.com', $banners[0]->getLink());
        $this->assertEquals('http://url-de-test-2.com', $banners[1]->getLink());
    }

    public function testGetMobileHomepageBanners()
    {
        $cmsService = new BannerService($this->getGuzzleClient());
        $banners = $cmsService->getHomepageBanners('mobile');

        $this->assertEquals(1, count($banners));
        $this->assertEquals('http://url-de-test.com', $banners[0]->getLink());
    }

    public function testGetDesktopHomepageBanners()
    {
        $cmsService = new BannerService($this->getGuzzleClient());
        $banners = $cmsService->getHomepageBanners('desktop');

        $this->assertEquals(1, count($banners));
        $this->assertEquals('http://url-de-test-2.com', $banners[0]->getLink());
    }

    public function testGetCategoryBanners()
    {
        $cmsService = new BannerService($this->getGuzzleClient());
        $banners = $cmsService->getCategoriesBanners(3);

        $this->assertEquals(2, count($banners));
        $this->assertEquals('http://url-de-test-2.com', $banners[0]->getLink());
        $this->assertEquals('http://url-de-test-3.com', $banners[1]->getLink());
    }

    public function testGetMobileCategoryBanners()
    {
        $cmsService = new BannerService($this->getGuzzleClient());
        $banners = $cmsService->getCategoriesBanners(3, 'mobile');

        $this->assertEquals(1, count($banners));
        $this->assertEquals('http://url-de-test-3.com', $banners[0]->getLink());
    }

    public function testGetDesktopCategoryBanners()
    {
        $cmsService = new BannerService($this->getGuzzleClient());
        $banners = $cmsService->getCategoriesBanners(3, 'desktop');

        $this->assertEquals(1, count($banners));
        $this->assertEquals('http://url-de-test-2.com', $banners[0]->getLink());
    }

    public function testGetEmptyCategoryBanners()
    {
        $cmsService = new BannerService($this->getGuzzleClient());
        $banners = $cmsService->getCategoriesBanners(1);

        $this->assertEmpty($banners);
    }
}
