<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Catalog;

use PHPUnit\Framework\TestCase;
use Wizaplace\SDK\Catalog\Option;

final class OptionTest extends TestCase
{
    public function testDeclinationOptionJsonSerialization()
    {
        $result = \json_encode(new Option([
            'id' => 7,
            'name' => 'size',
            'variants' => [
                [
                    'id' => 1,
                    'name' => '13',
                ],
                [
                    'id' => 2,
                    'name' => '15',
                ],
                [
                    'id' => 3,
                    'name' => '17',
                ],
                [
                    'id' => 4,
                    'name' => '21',
                    'image' => [
                        'id' => 1234,
                        'urls' => [
                            'original' => '//wathev.er/path/to/an/image.png',
                            'large' => '//wathev.er/path/to/an/image.png',
                            'medium' => '//wathev.er/path/to/an/image.png',
                            'small' => '//wathev.er/path/to/an/image.png',
                        ],
                    ],
                ],
            ],
        ]), JSON_PRETTY_PRINT);

        $expected = <<<JSON
{
    "id": 7,
    "name": "size",
    "position": 0,
    "variants": [
        {
            "id": 1,
            "name": "13",
            "image": null
        },
        {
            "id": 2,
            "name": "15",
            "image": null
        },
        {
            "id": 3,
            "name": "17",
            "image": null
        },
        {
            "id": 4,
            "name": "21",
            "image": {
                "id": 1234,
                "urls": {
                    "original": "\/\/wathev.er\/path\/to\/an\/image.png",
                    "large": "\/\/wathev.er\/path\/to\/an\/image.png",
                    "medium": "\/\/wathev.er\/path\/to\/an\/image.png",
                    "small": "\/\/wathev.er\/path\/to\/an\/image.png"
                }
            }
        }
    ]
}
JSON;
        $this->assertSame($expected, $result);
    }
}
