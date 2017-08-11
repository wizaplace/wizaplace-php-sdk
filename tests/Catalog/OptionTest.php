<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\Catalog;

use PHPUnit\Framework\TestCase;
use Wizaplace\Catalog\Option;

class OptionTest extends TestCase
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
                ],
            ],
        ]), JSON_PRETTY_PRINT);

        $expected = <<<JSON
{
    "id": 7,
    "name": "size",
    "variants": [
        {
            "id": 1,
            "name": "13"
        },
        {
            "id": 2,
            "name": "15"
        },
        {
            "id": 3,
            "name": "17"
        },
        {
            "id": 4,
            "name": "21"
        }
    ]
}
JSON;
        $this->assertSame($expected, $result);
    }
}
