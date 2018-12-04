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
 * @method static OptionStatus ENABLED()
 * @method static OptionStatus DISABLED()
 */
class OptionStatus extends Enum
{
    private const ENABLED = 'A';
    private const DISABLED = 'D';
}
