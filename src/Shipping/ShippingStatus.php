<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Shipping;

use MyCLabs\Enum\Enum;

/**
 * @method static ShippingStatus ENABLED()
 * @method static ShippingStatus DISABLED()
 */
final class ShippingStatus extends Enum
{
    private const ENABLED = 'A';
    private const DISABLED = 'D';
}
