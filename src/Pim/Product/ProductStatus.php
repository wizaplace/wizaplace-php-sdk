<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

use MyCLabs\Enum\Enum;

/**
 * Class ProductStatus
 * @package Wizaplace\SDK\Pim\Product
 *
 * @method static ProductStatus ENABLED()
 * @method static ProductStatus DISABLED()
 * @method static ProductStatus HIDDEN()
 */
final class ProductStatus extends Enum
{
    private const ENABLED = 'A';
    private const DISABLED = 'D';
    private const HIDDEN = 'H';
}
