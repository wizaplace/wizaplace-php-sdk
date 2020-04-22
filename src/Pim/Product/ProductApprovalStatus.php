<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

use MyCLabs\Enum\Enum;

/**
 * Class Product
 * @package Wizaplace\SDK\Pim\Product
 *
 * @method static ProductApprovalStatus APPROVED()
 * @method static ProductApprovalStatus REJECTED()
 * @method static ProductApprovalStatus PENDING()
 * @method static ProductApprovalStatus STANDBY()
 */
final class ProductApprovalStatus extends Enum
{
    private const APPROVED = 'Y';
    private const REJECTED = 'N';
    private const PENDING = 'P';
    private const STANDBY = 'S';
}
