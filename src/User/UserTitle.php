<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\User;

use MyCLabs\Enum\Enum;

/**
 * Class UserTitle
 * @package Wizaplace\SDK\User
 *
 * @method static UserTitle MR()
 * @method static UserTitle MRS()
 */
final class UserTitle extends Enum
{
    private const MR = 'mr';
    private const MRS = 'mrs';
}
