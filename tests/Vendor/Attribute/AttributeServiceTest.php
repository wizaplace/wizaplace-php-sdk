<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Vendor\Attribute;

use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Pim\Attribute\Attribute;
use Wizaplace\SDK\Pim\Attribute\AttributeType;
use Wizaplace\SDK\Pim\Attribute\AttributeVariant;
use Wizaplace\SDK\Tests\ApiTestCase;
use Wizaplace\SDK\Vendor\Attribute\AttributeService;

class AttributeServiceTest extends ApiTestCase
{
    public function testGetAttribute(): void
    {
        $attribute = $this->buildVendorAttributeService()->getAttribute(1);

        static::assertInstanceOf(Attribute::class, $attribute);
        static::assertSame(1, $attribute->getId());
        static::assertTrue(AttributeType::CHECKBOX_MULTIPLE()->equals($attribute->getType()));
        static::assertSame("Couleur", $attribute->getName());
        static::assertCount(0, $attribute->getCategoriesPath());
        static::assertCount(3, $attribute->getVariants());

        foreach ($attribute->getVariants() as $key => $variant) {
            static::assertInstanceOf(AttributeVariant::class, $variant);
            static::assertSame($key, $variant->getId());

            switch ($key) {
                case 1:
                    static::assertSame("Bleu", $variant->getName());
                    break;

                case 2:
                    static::assertSame("Blanc", $variant->getName());
                    break;

                case 3:
                    static::assertSame("Rouge", $variant->getName());
                    break;
            }
        }
    }

    public function testGetAttributeNotFound(): void
    {
        static::expectException(NotFound::class);

        $this->buildVendorAttributeService()->getAttribute(123);
    }

    protected function buildVendorAttributeService(string $email = 'vendor@world-company.com', string $password = 'password-vendor'): AttributeService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new AttributeService($apiClient);
    }
}
