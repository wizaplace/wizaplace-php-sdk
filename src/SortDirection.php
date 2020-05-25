<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK;

use MyCLabs\Enum\Enum;

/**
 * List of directions allowed for sorting search results.
 *
 * @method static SortDirection ASC()
 * @method static SortDirection DESC()
 */
class SortDirection extends Enum
{
    public const ASC = 'asc';
    public const DESC = 'desc';
}
