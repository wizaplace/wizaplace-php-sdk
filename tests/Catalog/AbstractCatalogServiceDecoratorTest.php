<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Catalog;

use PHPUnit\Framework\TestCase;
use Wizaplace\SDK\Catalog\AbstractCatalogServiceDecorator;
use Wizaplace\SDK\Catalog\CatalogServiceInterface;

class AbstractCatalogServiceDecoratorTest extends TestCase
{
    public function testItImplementsTheCatalogServiceInterfaceByItself()
    {
        $decorated = $this->createMock(CatalogServiceInterface::class);
        $decorator = new class($decorated) extends AbstractCatalogServiceDecorator {
        };

        // We check that extending the abstract class is enough to implement the interface
        $this->assertInstanceOf(CatalogServiceInterface::class, $decorator);
    }
}
