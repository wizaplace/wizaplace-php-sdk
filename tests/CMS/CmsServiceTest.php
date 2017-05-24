<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\CMS;

use FR3D\SwaggerAssertions\PhpUnit\Psr7AssertsTrait;
use VCR\VCR;
use Wizaplace\CMS\CmsService;
use Wizaplace\CMS\ObjectType;
use Wizaplace\CMS\SlugTarget;
use Wizaplace\Tests\ApiTest;

class CmsServiceTest extends ApiTest
{
    use Psr7AssertsTrait;

    public function testResolveSlugsForAllKinds()
    {
        try {
            VCR::turnOn();
            VCR::insertCassette($this->getName());

            $cmsService = new CmsService(self::getApiBaseUrl(), $this->getGuzzleClient());

            $slugTargets = $cmsService->resolveSlugs([
                'test-product-slug',
                'test-category-slug',
                'test-attribute-variant-slug',
                'test-cms-page-slug',
                'test-redirect-slug',
                '404-does-not-exist',
            ]);

            $this->assertCount(6, $slugTargets);
            foreach ([
                'test-product-slug' => ObjectType::PRODUCT(),
                'test-category-slug' => ObjectType::CATEGORY(),
                'test-attribute-variant-slug' => ObjectType::ATTRIBUTE_VARIANT(),
                'test-cms-page-slug' => ObjectType::CMS_PAGE(),
                'test-redirect-slug' => ObjectType::REDIRECT(),
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
        $cmsService = new CmsService(self::getApiBaseUrl(), $this->getGuzzleClient());
        $result = $cmsService->resolveSlugs([]);
        $this->assertEmpty($result);
    }

    public function testResolveProductSlug()
    {
        try {
            VCR::turnOn();
            VCR::insertCassette($this->getName());

            $cmsService = new CmsService(self::getApiBaseUrl(), $this->getGuzzleClient());

            $slugTarget = $cmsService->resolveSlug('test-product-slug');

            $this->assertNotNull($slugTarget);
            $this->assertEquals(ObjectType::PRODUCT(), $slugTarget->getObjectType());
        } finally {
            VCR::turnOff();
        }
    }

    public function testResolveSlugWhichDoesntExist()
    {
        try {
            VCR::turnOn();
            VCR::insertCassette($this->getName());

            $cmsService = new CmsService(self::getApiBaseUrl(), $this->getGuzzleClient());

            $slugTarget = $cmsService->resolveSlug('404-does-not-exist');

            $this->assertNull($slugTarget);
        } finally {
            VCR::turnOff();
        }
    }
}
