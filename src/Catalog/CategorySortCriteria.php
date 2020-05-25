<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

use MyCLabs\Enum\Enum;

/**
 * List of criteria allowed for sorting search results.
 *
 * @method static CategorySortCriteria NAME()
 * @method static CategorySortCriteria POSITION()
 * @method static CategorySortCriteria ID()
 * @method static CategorySortCriteria PRODUCT_COUNT()
 */
class CategorySortCriteria extends Enum
{
    public const NAME = "Name";
    public const POSITION = "Position";
    public const ID = "Id";
    public const PRODUCT_COUNT = "ProductCount";
}
