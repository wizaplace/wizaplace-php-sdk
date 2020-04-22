<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Pim\MultiVendorProduct;

use MyCLabs\Enum\Enum;

/**
 * Class MultiVendorProductStatus
 * @package Wizaplace\SDK\Pim\MultiVendorProduct
 *
 * @method static MultiVendorProductStatus ENABLED()
 * @method static MultiVendorProductStatus DISABLED()
 * @method static MultiVendorProductStatus HIDDEN()
 */
final class MultiVendorProductStatus extends Enum
{
    private const ENABLED = 'A';
    private const DISABLED = 'D';
    private const HIDDEN = 'H';
}
