<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

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

        $slugTargets = $seoService->resolveSlugs(
            [
                'ecran-pc-full-hd-24-noir',
                'informatique',
                'blanc',
                'test-cms-page-slug',
                'the-world-company-inc.',
                '404-does-not-exist',
            ]
        );

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

    public function testListSlugs(): void
    {
        //test without params
        $response = $this->buildSeoService()->listSlugs();
        static::assertSame(0, $response['offset']);
        static::assertSame(100, $response['limit']);
        static::assertGreaterThan(28, $response['total']);

        foreach ($response['items'] as $item) {
            static::assertInternalType('string', $item->getSlug());
            static::assertNotEmpty($item->getSlug());

            $target = $item->getTarget();
            static::assertInstanceOf(SlugTarget::class, $target);
            static::assertInternalType('string', $target->getObjectId());
            static::assertNotEmpty($target->getObjectId());
            static::assertInstanceOf(SlugTargetType::class, $target->getObjectType());
        }
    }

    public function testListSlugsWithOffSetAndLimit(): void
    {
        //test with limit = 3
        $response = $this->buildSeoService()->listSlugs(0, 3);
        static::assertSame(0, $response['offset']);
        static::assertSame(3, $response['limit']);
        static::assertSame(3, $response['total']);

        foreach ($response['items'] as $item) {
            static::assertInternalType('string', $item->getSlug());
            static::assertNotEmpty($item->getSlug());

            $target = $item->getTarget();
            static::assertInstanceOf(SlugTarget::class, $target);
            static::assertInternalType('string', $target->getObjectId());
            static::assertNotEmpty($target->getObjectId());
            static::assertInstanceOf(SlugTargetType::class, $target->getObjectType());
        }

        //test with limit = 10
        $response = $this->buildSeoService()->listSlugs(0, 10);
        static::assertSame(0, $response['offset']);
        static::assertSame(10, $response['limit']);
        static::assertSame(10, $response['total']);

        foreach ($response['items'] as $item) {
            $this->assertInternalType('string', $item->getSlug());
            $this->assertNotEmpty($item->getSlug());

            $target = $item->getTarget();
            $this->assertInstanceOf(SlugTarget::class, $target);
            $this->assertInternalType('string', $target->getObjectId());
            $this->assertNotEmpty($target->getObjectId());
            $this->assertInstanceOf(SlugTargetType::class, $target->getObjectType());
        }

        //test with offset = 10 limit = 5
        $response = $this->buildSeoService()->listSlugs(10, 5);
        static::assertSame(10, $response['offset']);
        static::assertSame(5, $response['limit']);
        static::assertSame(5, $response['total']);

        foreach ($response['items'] as $item) {
            $this->assertInternalType('string', $item->getSlug());
            $this->assertNotEmpty($item->getSlug());

            $target = $item->getTarget();
            $this->assertInstanceOf(SlugTarget::class, $target);
            $this->assertInternalType('string', $target->getObjectId());
            $this->assertNotEmpty($target->getObjectId());
            $this->assertInstanceOf(SlugTargetType::class, $target->getObjectType());
        }
    }

    private function buildSeoService(): SeoService
    {
        return new SeoService($this->buildApiClient());
    }
}
