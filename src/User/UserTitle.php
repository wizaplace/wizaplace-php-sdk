<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\User;

use MyCLabs\Enum\Enum;

/**
 * @method static UserTitle MR()
 * @method static UserTitle MRS()
 */
class UserTitle extends Enum
{
    const MR = 'mr';
    const MRS = 'mrs';
}
