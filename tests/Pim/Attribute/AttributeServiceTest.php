<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Pim\Attribute;

use Wizaplace\SDK\Pim\Attribute\Attribute;
use Wizaplace\SDK\Pim\Attribute\AttributeService;
use Wizaplace\SDK\Pim\Attribute\AttributeType;
use Wizaplace\SDK\Pim\Attribute\AttributeVariant;
use Wizaplace\SDK\Pim\Attribute\ProductAttribute;
use Wizaplace\SDK\Pim\Attribute\ProductAttributeValue;
use Wizaplace\SDK\Pim\Attribute\ProductAttributeVariants;
use Wizaplace\SDK\Tests\ApiTestCase;

class AttributeServiceTest extends ApiTestCase
{
    public function testGetProductAttributes()
    {
        $attributes = $this->buildAttributeService()->getProductAttributes(8);

        $this->assertContainsOnly(ProductAttribute::class, $attributes);
        $this->assertGreaterThanOrEqual(7, \count($attributes));

        foreach ($attributes as $attribute) {
            $this->assertNotEmpty($attribute->getName());
            $this->assertGreaterThan(0, $attribute->getId());
            $this->assertContainsOnly('int', $attribute->getCategoriesPath());
            $this->assertInstanceOf(AttributeType::class, $attribute->getType());

            if ($attribute instanceof ProductAttributeValue) {
                $this->assertNotEmpty($attribute->getValue());
            } elseif ($attribute instanceof ProductAttributeVariants) {
                $variants = $attribute->getSelectedVariants();
                $this->assertContainsOnly(AttributeVariant::class, $variants);

                foreach ($variants as $variant) {
                    $this->assertNotEmpty($variant->getName());
                    $this->assertGreaterThan(0, $variant->getId());
                }
            }
        }
    }

    public function testGetCategoryAttributes()
    {
        $attributes = $this->buildAttributeService()->getCategoryAttributes(6);

        $this->assertContainsOnly(Attribute::class, $attributes);
        $this->assertGreaterThanOrEqual(7, \count($attributes));

        foreach ($attributes as $attribute) {
            $this->assertNotEmpty($attribute->getName());
            $this->assertGreaterThan(0, $attribute->getId());
            $this->assertContainsOnly('int', $attribute->getCategoriesPath());
            $this->assertInstanceOf(AttributeType::class, $attribute->getType());
            $variants = $attribute->getVariants();
            if ($variants !== null) {
                $this->assertContainsOnly(AttributeVariant::class, $variants);
                foreach ($variants as $variant) {
                    $this->assertNotEmpty($variant->getName());
                    $this->assertGreaterThan(0, $variant->getId());
                }
            }
        }
    }

    public function testGetCategoryAttributesWithParentId(): void
    {
        $attributes = $this->buildAttributeService()->getCategoryAttributes(6);
        static::assertContainsOnly(Attribute::class, $attributes);
        static::assertGreaterThan(0, $attributes[6]->getId());
        //Test with parent_id = 4
        static::assertSame(4, $attributes[6]->getParentId());

        $attributes = $this->buildAttributeService()->getCategoryAttributes(1);
        static::assertContainsOnly(Attribute::class, $attributes);
        //Test parent_id = 0
        static::assertSame(0, $attributes[0]->getParentId());
    }

    public function testGetCategoryAttributesWithCode(): void
    {
        $attributes = $this->buildAttributeService()->getCategoryAttributes(1);
        static::assertContainsOnly(Attribute::class, $attributes);
        static::assertGreaterThan(0, $attributes[3]->getId());

        //with code
        static::assertSame('Couleur 2', $attributes[3]->getName());
        static::assertSame('azerty 6', $attributes[3]->getCode());

        //without code
        static::assertSame('color', $attributes[1]->getName());
        static::assertSame('', $attributes[1]->getCode());
    }

    public function testGetProductAttributeWithParentId()
    {
        /** @var ProductAttributeVariants $attribute */
        $attribute = $this->buildAttributeService()->getProductAttribute(8, 1);
        static::assertInstanceOf(ProductAttributeVariants::class, $attribute);
        static::assertSame(1, $attribute->getId());
        static::assertSame(0, $attribute->getParentId());
    }

    public function testGetProductAttributeVariants()
    {
        /** @var ProductAttributeVariants $attribute */
        $attribute = $this->buildAttributeService()->getProductAttribute(8, 1);

        $this->assertInstanceOf(ProductAttributeVariants::class, $attribute);
        $this->assertSame(1, $attribute->getId());
        $this->assertSame('Couleur', $attribute->getName());
        $this->assertTrue(AttributeType::CHECKBOX_MULTIPLE()->equals($attribute->getType()));

        $variants = $attribute->getSelectedVariants();
        $this->assertContainsOnly(AttributeVariant::class, $variants);
        $this->assertCount(2, $variants);

        $this->assertSame(2, $variants[2]->getId());
        $this->assertSame('Blanc', $variants[2]->getName());

        $this->assertSame(3, $variants[3]->getId());
        $this->assertSame('Rouge', $variants[3]->getName());
    }

    public function testGetProductAttributeValue()
    {
        /** @var ProductAttributeValue $attribute */
        $attribute = $this->buildAttributeService()->getProductAttribute(8, 3);

        $this->assertInstanceOf(ProductAttributeValue::class, $attribute);
        $this->assertSame(3, $attribute->getId());
        $this->assertSame('Commentaire', $attribute->getName());
        $this->assertTrue(AttributeType::FREE_TEXT()->equals($attribute->getType()));
        $this->assertSame('Commentaire #12M%M_°09£*/.?', $attribute->getValue());
    }

    public function testSetProductAttributeValue()
    {
        $this->buildAttributeService()->setProductAttributeValue(8, 3, 'different comment');

        /** @var ProductAttributeValue $attribute */
        $attribute = $this->buildAttributeService()->getProductAttribute(8, 3);
        $this->assertSame('different comment', $attribute->getValue());
    }

    public function testSetProductAttributeVariants()
    {
        $this->buildAttributeService()->setProductAttributeVariants(8, 1, [3, 1]);

        /** @var ProductAttributeVariants $attribute */
        $attribute = $this->buildAttributeService()->getProductAttribute(8, 1);
        $this->assertSame(
            [1 => 1, 3 => 3],
            array_map(
                function (AttributeVariant $variant): int {
                    return $variant->getId();
                },
                $attribute->getSelectedVariants()
            )
        );
    }

    private function buildAttributeService($userEmail = 'admin@wizaplace.com', $userPassword = 'password'): AttributeService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($userEmail, $userPassword);

        return new AttributeService($apiClient);
    }
}
