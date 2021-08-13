<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Catalog;

use Wizaplace\SDK\Catalog\Facet\NumericFacet;
use PHPUnit\Framework\TestCase;

class NumericFacetTest extends TestCase
{
    public function testInit()
    {
        $data = ['name' => 'Fafa', 'label' => 'Lead'];

        // Without given values
        $facet = new NumericFacet($data);

        static::assertEquals(0, $facet->getMin());
        static::assertEquals(PHP_INT_MAX, $facet->getMax());

        // With Specific values
        $data['values']['min'] = 5;
        $data['values']['max'] = 20;
        $facet = new NumericFacet($data);

        static::assertEquals(5, $facet->getMin());
        static::assertEquals(20, $facet->getMax());
    }
}
