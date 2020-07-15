<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\User;

use MyCLabs\Enum\Enum;

/**
 * Class AddressType
 * @package Wizaplace\SDK\User
 *
 * @method static AddressType BILLING()
 * @method static AddressType SHIPPING()
 */
final class AddressType extends Enum
{
    protected const BILLING = 'billing';
    protected const SHIPPING = 'shipping';
}
