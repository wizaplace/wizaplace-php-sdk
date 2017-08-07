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
        $cmsService = $this->buildBannerService();
        $banners = $cmsService->getHomepageBanners();

        $this->assertEquals(2, count($banners));

        $this->assertEquals('http://url-de-test.com', $banners[0]->getLink());
        $this->assertEquals(3, $banners[0]->getImageId());
        $this->assertTrue($banners[0]->getShouldOpenInNewWindow());

        $this->assertEquals('http://url-de-test-2.com', $banners[1]->getLink());
        $this->assertEquals(4, $banners[1]->getImageId());
        $this->assertFalse($banners[1]->getShouldOpenInNewWindow());
    }

    public function testGetMobileHomepageBanners()
    {
        $cmsService = $this->buildBannerService();
        $banners = $cmsService->getHomepageBanners('mobile');

        $this->assertEquals(1, count($banners));
        $this->assertEquals('http://url-de-test.com', $banners[0]->getLink());
        $this->assertEquals(3, $banners[0]->getImageId());
        $this->assertTrue($banners[0]->getShouldOpenInNewWindow());
    }

    public function testGetDesktopHomepageBanners()
    {
        $cmsService = $this->buildBannerService();
        $banners = $cmsService->getHomepageBanners('desktop');

        $this->assertEquals(1, count($banners));
        $this->assertEquals('http://url-de-test-2.com', $banners[0]->getLink());
        $this->assertEquals(4, $banners[0]->getImageId());
        $this->assertFalse($banners[0]->getShouldOpenInNewWindow());
    }

    public function testGetCategoryBanners()
    {
        $cmsService = $this->buildBannerService();
        $banners = $cmsService->getCategoriesBanners(3);

        $this->assertEquals(2, count($banners));

        $this->assertEquals('http://url-de-test-2.com', $banners[0]->getLink());
        $this->assertEquals(4, $banners[0]->getImageId());
        $this->assertFalse($banners[0]->getShouldOpenInNewWindow());

        $this->assertEquals('http://url-de-test-3.com', $banners[1]->getLink());
        $this->assertEquals(5, $banners[1]->getImageId());
        $this->assertFalse($banners[1]->getShouldOpenInNewWindow());
    }

    public function testGetMobileCategoryBanners()
    {
        $cmsService = $this->buildBannerService();
        $banners = $cmsService->getCategoriesBanners(3, 'mobile');

        $this->assertEquals(1, count($banners));
        $this->assertEquals('http://url-de-test-3.com', $banners[0]->getLink());
        $this->assertEquals(5, $banners[0]->getImageId());
        $this->assertFalse($banners[0]->getShouldOpenInNewWindow());
    }

    public function testGetDesktopCategoryBanners()
    {
        $cmsService = $this->buildBannerService();
        $banners = $cmsService->getCategoriesBanners(3, 'desktop');

        $this->assertEquals(1, count($banners));
        $this->assertEquals('http://url-de-test-2.com', $banners[0]->getLink());
        $this->assertEquals(4, $banners[0]->getImageId());
        $this->assertFalse($banners[0]->getShouldOpenInNewWindow());
    }

    public function testGetEmptyCategoryBanners()
    {
        $cmsService = $this->buildBannerService();
        $banners = $cmsService->getCategoriesBanners(1);

        $this->assertEmpty($banners);
    }

    public function testGetNonExistentCategoryBanners()
    {
        $cmsService = $this->buildBannerService();
        $banners = $cmsService->getCategoriesBanners(404);

        $this->assertEmpty($banners);
    }

    private function buildBannerService(): BannerService
    {
        return new BannerService($this->buildApiClient());
    }
}
