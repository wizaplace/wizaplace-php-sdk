<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\Seo;

use FR3D\SwaggerAssertions\PhpUnit\Psr7AssertsTrait;
use VCR\VCR;
use Wizaplace\Seo\SeoService;
use Wizaplace\Seo\SlugTargetType;
use Wizaplace\Seo\SlugTarget;
use Wizaplace\Tests\ApiTest;

class SeoServiceTest extends ApiTest
{
    use Psr7AssertsTrait;

    public function testResolveSlugsForAllKinds()
    {
        try {
            VCR::turnOn();
            VCR::insertCassette($this->getName());

            $cmsService = new SeoService(self::getApiBaseUrl(), $this->getGuzzleClient());

            $slugTargets = $cmsService->resolveSlugs([
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
        } finally {
            VCR::turnOff();
        }
    }

    public function testResolveEmptySlugs()
    {
        $cmsService = new SeoService(self::getApiBaseUrl(), $this->getGuzzleClient());
        $result = $cmsService->resolveSlugs([]);
        $this->assertEmpty($result);
    }

    public function testResolveProductSlug()
    {
        try {
            VCR::turnOn();
            VCR::insertCassette($this->getName());

            $cmsService = new SeoService(self::getApiBaseUrl(), $this->getGuzzleClient());

            $slugTarget = $cmsService->resolveSlug('test-product-slug');

            $this->assertNotNull($slugTarget);
            $this->assertEquals(SlugTargetType::PRODUCT(), $slugTarget->getObjectType());
        } finally {
            VCR::turnOff();
        }
    }

    public function testResolveSlugWhichDoesntExist()
    {
        try {
            VCR::turnOn();
            VCR::insertCassette($this->getName());

            $cmsService = new SeoService(self::getApiBaseUrl(), $this->getGuzzleClient());

            $slugTarget = $cmsService->resolveSlug('404-does-not-exist');

            $this->assertNull($slugTarget);
        } finally {
            VCR::turnOff();
        }
    }
}
