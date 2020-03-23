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
 * @method static CompanyStatus NEW()
 * @method static CompanyStatus PENDING()
 * @method static CompanyStatus ENABLED()
 * @method static CompanyStatus DISABLED()
 */
class CompanyStatus extends Enum
{
    public const NEW = 'NEW';
    public const PENDING = 'PENDING';
    public const ENABLED = 'ENABLED';
    public const DISABLED = 'DISABLED';
}
