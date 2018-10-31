<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\User;

use MyCLabs\Enum\Enum;

/**
 * @method static UserType ADMIN()
 * @method static UserType VENDOR()
 */
final class UserType extends Enum
{
    const ADMIN = 'A';
    const VENDOR = 'V';
}
