<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\Seo;

use Wizaplace\SDK\Seo\SeoService;
use Wizaplace\SDK\Seo\SlugTarget;
use Wizaplace\SDK\Seo\SlugTargetType;
use Wizaplace\SDK\Tests\ApiTestCase;

final class SeoServiceTest extends ApiTestCase
{
    public function testResolveSlugsForAllKinds()
    {
        $seoService = $this->buildSeoService();

        $slugTargets = $seoService->resolveSlugs([
            'ecran-pc-full-hd-24-noir',
            'informatique',
            'blanc',
            'test-cms-page-slug',
            'the-world-company-inc.',
            '404-does-not-exist',
        ]);

        $this->assertCount(6, $slugTargets);
        foreach ([
            'ecran-pc-full-hd-24-noir' => SlugTargetType::PRODUCT(),
            'informatique' => SlugTargetType::CATEGORY(),
            'blanc' => SlugTargetType::ATTRIBUTE_VARIANT(),
            'the-world-company-inc.' => SlugTargetType::COMPANY(),
            'test-cms-page-slug' => SlugTargetType::CMS_PAGE(),
        ] as $key => $objectType) {
            $this->assertArrayHasKey($key, $slugTargets);
            $this->assertInstanceOf(SlugTarget::class, $slugTargets[$key], "for slug $key");
            $this->assertTrue($objectType->equals($slugTargets[$key]->getObjectType()));
        }

        $this->assertArrayHasKey('404-does-not-exist', $slugTargets);
        $this->assertNull($slugTargets['404-does-not-exist']);
    }

    public function testResolveEmptySlugs()
    {
        $seoService = $this->buildSeoService();
        $result = $seoService->resolveSlugs([]);
        $this->assertEmpty($result);
    }

    public function testResolveProductSlug()
    {
        $seoService = $this->buildSeoService();

        $slugTarget = $seoService->resolveSlug('test-product-slug');

        $this->assertNotNull($slugTarget);
        $this->assertTrue(SlugTargetType::PRODUCT()->equals($slugTarget->getObjectType()));
        $this->assertSame('1', $slugTarget->getObjectId());
    }

    public function testResolveSlugWhichDoesntExist()
    {
        $seoService = $this->buildSeoService();

        $slugTarget = $seoService->resolveSlug('404-does-not-exist');

        $this->assertNull($slugTarget);
    }

    private function buildSeoService(): SeoService
    {
        return new SeoService($this->buildApiClient());
    }
}
