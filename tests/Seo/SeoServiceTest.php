<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\Seo;

use FR3D\SwaggerAssertions\PhpUnit\Psr7AssertsTrait;
use Wizaplace\Seo\SeoService;
use Wizaplace\Seo\SlugTarget;
use Wizaplace\Seo\SlugTargetType;
use Wizaplace\Tests\ApiTestCase;

class SeoServiceTest extends ApiTestCase
{
    use Psr7AssertsTrait;

    public function testResolveSlugsForAllKinds()
    {
        $seoService = new SeoService($this->getGuzzleClient());

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

        // @FIXME: small hack due to php forcing content-type
        static::$historyContainer = [];
    }

    public function testResolveEmptySlugs()
    {
        $seoService = new SeoService($this->getGuzzleClient());
        $result = $seoService->resolveSlugs([]);
        $this->assertEmpty($result);
    }

    public function testResolveProductSlug()
    {
        $seoService = new SeoService($this->getGuzzleClient());

        $slugTarget = $seoService->resolveSlug('test-product-slug');

        $this->assertNotNull($slugTarget);
        $this->assertEquals(SlugTargetType::PRODUCT(), $slugTarget->getObjectType());
    }

    public function testResolveSlugWhichDoesntExist()
    {
        $seoService = new SeoService($this->getGuzzleClient());

        $slugTarget = $seoService->resolveSlug('404-does-not-exist');

        $this->assertNull($slugTarget);
    }
}
