<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

use MyCLabs\Enum\Enum;

/**
 * Class CompanySummary
 * @package Wizaplace\SDK\Catalog
 *
 * @method static Condition BRAND_NEW()
 * @method static Condition USED()
 */
final class Condition extends Enum
{
    private const BRAND_NEW = 'N';
    private const USED = 'U';
}
