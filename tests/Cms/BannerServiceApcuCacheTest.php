<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Cms;

use Wizaplace\SDK\Cms\BannerService;
use Wizaplace\SDK\Cms\BannerServiceApcuCache;
use Wizaplace\SDK\Tests\ApiTestCase;

class BannerServiceApcuCacheTest extends ApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (!function_exists('apcu_cache_info')) {
            $this->markTestSkipped('apcu is not installed');
        }
        if (apcu_cache_info(true) === false) {
            $this->markTestSkipped('apcu is not enabled');
        }
    }

    public function testGetHomepageBanners()
    {
        apcu_clear_cache();
        $service = $this->buildBannerService();

        $requestExpectedCount = 0;
        $this->assertCount($requestExpectedCount, self::$historyContainer);

        // Desktop
        $desktopBanners = $service->getHomepageBanners('desktop');
        $requestExpectedCount++;
        $this->assertCount($requestExpectedCount, self::$historyContainer);
        $this->assertNotEmpty($desktopBanners);

        // check that on a second call, the result is the same and no request was made
        $cachedDesktopBanners = $service->getHomepageBanners('desktop');
        $this->assertCount($requestExpectedCount, self::$historyContainer);
        $this->assertEquals($desktopBanners, $cachedDesktopBanners);

        // check that on another call with a new service, the result is the same and no request was made
        $cachedDesktopBanners = $this->buildBannerService()->getHomepageBanners('desktop');
        $this->assertCount($requestExpectedCount, self::$historyContainer);
        $this->assertEquals($desktopBanners, $cachedDesktopBanners);

        // Mobile
        $mobileBanners = $service->getHomepageBanners('mobile');
        $requestExpectedCount++;
        $this->assertCount($requestExpectedCount, self::$historyContainer);
        $this->assertNotEmpty($mobileBanners);

        // check that on a second call, the result is the same and no request was made
        $cachedMobileBanners = $service->getHomepageBanners('mobile');
        $this->assertCount($requestExpectedCount, self::$historyContainer);
        $this->assertEquals($mobileBanners, $cachedMobileBanners);

        // check that on another call with a new service, the result is the same and no request was made
        $cachedMobileBanners = $this->buildBannerService()->getHomepageBanners('mobile');
        $this->assertCount($requestExpectedCount, self::$historyContainer);
        $this->assertEquals($mobileBanners, $cachedMobileBanners);
    }

    public function testGetCategoriesBanners()
    {
        apcu_clear_cache();
        $service = $this->buildBannerService();

        $requestExpectedCount = 0;
        $this->assertCount($requestExpectedCount, self::$historyContainer);

        // Desktop
        $desktopBanners = $service->getCategoriesBanners(3, 'desktop');
        $requestExpectedCount++;
        $this->assertCount($requestExpectedCount, self::$historyContainer);

        // check that on a second call, the result is the same and no request was made
        $cachedDesktopBanners = $service->getCategoriesBanners(3, 'desktop');
        $this->assertCount($requestExpectedCount, self::$historyContainer);
        $this->assertEquals($desktopBanners, $cachedDesktopBanners);

        // check that on another call with a new service, the result is the same and no request was made
        $cachedDesktopBanners = $this->buildBannerService()->getCategoriesBanners(3, 'desktop');
        $this->assertCount($requestExpectedCount, self::$historyContainer);
        $this->assertEquals($desktopBanners, $cachedDesktopBanners);

        // Mobile
        $mobileBanners = $service->getCategoriesBanners(3, 'mobile');
        $requestExpectedCount++;
        $this->assertCount($requestExpectedCount, self::$historyContainer);
        $this->assertNotEmpty($mobileBanners);

        // check that on a second call, the result is the same and no request was made
        $cachedMobileBanners = $service->getCategoriesBanners(3, 'mobile');
        $this->assertCount($requestExpectedCount, self::$historyContainer);
        $this->assertEquals($mobileBanners, $cachedMobileBanners);

        // check that on another call with a new service, the result is the same and no request was made
        $cachedMobileBanners = $this->buildBannerService()->getCategoriesBanners(3, 'mobile');
        $this->assertCount($requestExpectedCount, self::$historyContainer);
        $this->assertEquals($mobileBanners, $cachedMobileBanners);
    }

    private function buildBannerService(): BannerServiceApcuCache
    {
        return new BannerServiceApcuCache(new BannerService($this->buildApiClient()), 600);
    }
}
