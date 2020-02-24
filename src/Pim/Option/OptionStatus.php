<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Option;

use MyCLabs\Enum\Enum;

/**
 * Class OptionStatus
 * @package Wizaplace\SDK\Pim\Option
 *
 * @method static OptionStatus ENABLED()
 * @method static OptionStatus DISABLED()
 */
class OptionStatus extends Enum
{
    private const ENABLED = 'A';
    private const DISABLED = 'D';
}
