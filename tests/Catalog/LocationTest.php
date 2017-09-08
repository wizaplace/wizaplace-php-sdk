<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\Catalog;

use PHPUnit\Framework\TestCase;
use Wizaplace\SDK\Catalog\Location;

final class LocationTest extends TestCase
{
    public function testInterface()
    {
        $location = new Location(27.9878, 86.9250);

        $this->assertSame(27.9878, $location->getLatitude());
        $this->assertSame(86.9250, $location->getLongitude());
    }
}
