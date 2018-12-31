<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\Catalog;

use PHPUnit\Framework\TestCase;
use Wizaplace\SDK\Catalog\DeclinationOption;

final class DeclinationOptionTest extends TestCase
{
    public function testDeclinationOptionJsonSerialization()
    {
        $result = \json_encode(new DeclinationOption([
            'id' => 1,
            'name' => 'size',
            'variantId' => 1,
            'variantName' => 'xs',
        ]), JSON_PRETTY_PRINT);

        $expected = <<<JSON
{
    "id": 1,
    "name": "size",
    "variantId": 1,
    "variantName": "xs",
    "image": null,
    "position": 0
}
JSON;
        $this->assertSame($expected, $result);
    }
}
