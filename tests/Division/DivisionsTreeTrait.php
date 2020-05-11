<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Division;

use Wizaplace\SDK\Division\Division;
use Wizaplace\SDK\Division\DivisionsTreeFilters;

trait DivisionsTreeTrait
{
    /** For this test, we suppose than we have one root division (ALL) */
    public function assertDivisionTree(array $divisionsTree): void
    {
        static::assertCount(1, $divisionsTree);

        $rootDivision = array_shift($divisionsTree);

        // Check the root divisions
        static::assertInstanceOf(Division::class, $rootDivision);
        static::assertSame('ALL', $rootDivision->getCode());
        static::assertSame(0, $rootDivision->getLevel());
        static::assertFalse(false, $rootDivision->isEnabled());
        static::assertSame('Toutes les divisions', $rootDivision->getName());
        static::assertTrue($rootDivision->getChildren() > 0);

        // Check if we have valid children
        foreach ($rootDivision->getChildren() as $child) {
            static::assertInstanceOf(Division::class, $child);
            static::assertSame(1, $child->getLevel());
        }
    }

    public function assertDivisionTreeFiltersEnabled(array $divisionsTree, bool $expectedIsEnabled)
    {
        static::assertCount(1, $divisionsTree);

        $rootDivision = array_shift($divisionsTree);

        static::assertInstanceOf(Division::class, $rootDivision);
        static::assertSame($expectedIsEnabled, $rootDivision->isEnabled());
        static::assertTrue($rootDivision->getChildren() > 0);
    }

    public function assertDivisionTreeFiltersRootCode(array $divisionsTree, string $expectedRootCode)
    {
        static::assertCount(1, $divisionsTree);

        $rootDivision = array_shift($divisionsTree);

        static::assertInstanceOf(Division::class, $rootDivision);
        static::assertEquals($expectedRootCode, $rootDivision->getCode());
        static::assertTrue($rootDivision->getChildren() > 0);
    }
}
