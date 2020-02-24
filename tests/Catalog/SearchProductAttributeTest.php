<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Catalog;

use PHPUnit\Framework\TestCase;
use Wizaplace\SDK\Catalog\AttributeType;
use Wizaplace\SDK\Catalog\ProductAttributeValue;
use Wizaplace\SDK\Catalog\SearchProductAttribute;

final class SearchProductAttributeTest extends TestCase
{
    public function testInterface()
    {
        $jsonData = <<<JSON
{
	"attribute": {
		"id": 52,
		"name": "Couleur test",
		"type": "M",
		"position": 10,
		"parentId": null,
		"code": ""
	},
	"values": [{
		"id": 64,
		"attributeId": 52,
		"name": "blue",
		"slug": "blue-color",
		"image": {
			"id": 708
		}
	}]
}
JSON;

        $attribute = new SearchProductAttribute(json_decode($jsonData, true));

        $this->assertSame(52, $attribute->getId());
        $this->assertSame('', $attribute->getSlug()); // @FIXME @TODO : the API does not give us this data. It should be added to the API, or removed from the SDK
        $this->assertTrue(AttributeType::CHECKBOX_MULTIPLE()->equals($attribute->getType()));
        $this->assertSame('Couleur test', $attribute->getName());
        $this->assertEquals(
            [
                new ProductAttributeValue(
                    [
                        'id' => 64,
                        'attributeId' => 52,
                        'name' => 'blue',
                        'slug' => 'blue-color',
                        'image' => [
                            'id' => 708,
                        ],
                    ]
                ),
            ],
            $attribute->getValues()
        );
    }
}
