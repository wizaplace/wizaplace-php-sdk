<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\Cms;

use Wizaplace\SDK\Cms\BannerService;
use Wizaplace\SDK\Tests\ApiTestCase;

final class BannerServiceTest extends ApiTestCase
{
    public function testGetHomepageBanners()
    {
        $cmsService = $this->buildBannerService();
        $banners = $cmsService->getHomepageBanners();

        $this->assertSame(2, count($banners));

        $this->assertSame('http://url-de-test.com', (string) $banners[0]->getLink());
        $this->assertSame(3, $banners[0]->getImageId());
        $this->assertTrue($banners[0]->getShouldOpenInNewWindow());

        $this->assertSame('http://url-de-test-2.com', (string) $banners[1]->getLink());
        $this->assertSame(4, $banners[1]->getImageId());
        $this->assertFalse($banners[1]->getShouldOpenInNewWindow());
    }

    public function testGetMobileHomepageBanners()
    {
        $cmsService = $this->buildBannerService();
        $banners = $cmsService->getHomepageBanners('mobile');

        $this->assertSame(1, count($banners));
        $this->assertSame('http://url-de-test.com', (string) $banners[0]->getLink());
        $this->assertSame(3, $banners[0]->getImageId());
        $this->assertTrue($banners[0]->getShouldOpenInNewWindow());
    }

    public function testGetDesktopHomepageBanners()
    {
        $cmsService = $this->buildBannerService();
        $banners = $cmsService->getHomepageBanners('desktop');

        $this->assertSame(1, count($banners));
        $this->assertSame('http://url-de-test-2.com', (string) $banners[0]->getLink());
        $this->assertSame(4, $banners[0]->getImageId());
        $this->assertFalse($banners[0]->getShouldOpenInNewWindow());
    }

    public function testGetCategoryBanners()
    {
        $cmsService = $this->buildBannerService();
        $banners = $cmsService->getCategoriesBanners(3);

        $this->assertSame(2, count($banners));

        $this->assertSame('http://url-de-test-2.com', (string) $banners[0]->getLink());
        $this->assertSame(4, $banners[0]->getImageId());
        $this->assertFalse($banners[0]->getShouldOpenInNewWindow());

        $this->assertSame('http://url-de-test-3.com', (string) $banners[1]->getLink());
        $this->assertSame(5, $banners[1]->getImageId());
        $this->assertFalse($banners[1]->getShouldOpenInNewWindow());
    }

    public function testGetMobileCategoryBanners()
    {
        $cmsService = $this->buildBannerService();
        $banners = $cmsService->getCategoriesBanners(3, 'mobile');

        $this->assertSame(1, count($banners));
        $this->assertSame('http://url-de-test-3.com', (string) $banners[0]->getLink());
        $this->assertSame(5, $banners[0]->getImageId());
        $this->assertFalse($banners[0]->getShouldOpenInNewWindow());
    }

    public function testGetDesktopCategoryBanners()
    {
        $cmsService = $this->buildBannerService();
        $banners = $cmsService->getCategoriesBanners(3, 'desktop');

        $this->assertSame(1, count($banners));
        $this->assertSame('http://url-de-test-2.com', (string) $banners[0]->getLink());
        $this->assertSame(4, $banners[0]->getImageId());
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
