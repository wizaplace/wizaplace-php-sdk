<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\User;

use MyCLabs\Enum\Enum;

/**
 * Class UserType
 * @package Wizaplace\SDK\User
 *
 * @method static UserType ADMIN()
 * @method static UserType CLIENT()
 * @method static UserType VENDOR()
 */
final class UserType extends Enum
{
    private const ADMIN = 'A';
    private const CLIENT = 'C';
    private const VENDOR = 'V';
}
