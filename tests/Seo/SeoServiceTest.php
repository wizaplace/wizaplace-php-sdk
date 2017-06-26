<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\Seo;

use Wizaplace\Seo\SeoService;
use Wizaplace\Seo\SlugTarget;
use Wizaplace\Seo\SlugTargetType;
use Wizaplace\Tests\ApiTestCase;

class SeoServiceTest extends ApiTestCase
{
    public function testResolveSlugsForAllKinds()
    {
        $seoService = $this->buildSeoService();

        $slugTargets = $seoService->resolveSlugs([
            'test-product-slug',
            'test-category-slug',
            'test-attribute-variant-slug',
            'test-cms-page-slug',
            'test-company-slug',
            '404-does-not-exist',
        ]);

        $this->assertCount(6, $slugTargets);
        foreach ([
            'test-product-slug' => SlugTargetType::PRODUCT(),
            'test-category-slug' => SlugTargetType::CATEGORY(),
            'test-attribute-variant-slug' => SlugTargetType::ATTRIBUTE_VARIANT(),
            'test-company-slug' => SlugTargetType::COMPANY(),
            'test-cms-page-slug' => SlugTargetType::CMS_PAGE(),
        ] as $key => $objectType) {
            $this->assertArrayHasKey($key, $slugTargets);
            $this->assertInstanceOf(SlugTarget::class, $slugTargets[$key], "for slug $key");
            $this->assertEquals($objectType, $slugTargets[$key]->getObjectType());
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
        $this->assertEquals(SlugTargetType::PRODUCT(), $slugTarget->getObjectType());
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
