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
final class UserTitle extends Enum
{
    private const MR = 'mr';
    private const MRS = 'mrs';
}
