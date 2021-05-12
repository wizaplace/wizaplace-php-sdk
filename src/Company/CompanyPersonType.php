<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Company;

use MyCLabs\Enum\Enum;

/**
 * Manage type of companyPerson
 *
 * @method static CompanyPersonType OWNER()
 */
class CompanyPersonType extends Enum
{
    const OWNER = 'owner';
}
