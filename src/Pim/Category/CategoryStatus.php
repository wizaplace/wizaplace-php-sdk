<?php

/**
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Category;

use MyCLabs\Enum\Enum;

/**
 * Class CategoryStatus
 * @package Wizaplace\SDK\Pim\Category
 *
 * @method static CategoryStatus ENABLED()
 * @method static CategoryStatus DISABLED()
 * @method static CategoryStatus HIDDEN()
 */
class CategoryStatus extends Enum
{
    private const ENABLED = 'A';
    private const DISABLED = 'D';
    private const HIDDEN = 'H';
}
