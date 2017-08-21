<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\Catalog;

use PHPUnit\Framework\TestCase;
use Wizaplace\Catalog\DeclinationOption;

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
    "variantName": "xs"
}
JSON;
        $this->assertSame($expected, $result);
    }
}
