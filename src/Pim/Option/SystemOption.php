<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Option;

use MyCLabs\Enum\Enum;

/**
 * @method static SystemOption PAYMENT_FREQUENCY()
 * @method static SystemOption COMMITMENT_PERIOD()
 */
final class SystemOption extends Enum
{
    private const PAYMENT_FREQUENCY = 'payment_frequency';
    private const COMMITMENT_PERIOD = 'commitment_period';
}
