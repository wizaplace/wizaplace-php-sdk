<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Exception;

use MyCLabs\Enum\Enum;

/**
 * @internal
 * @method static ErrorCode BASKET_NOT_FOUND()
 * @method static ErrorCode COUPON_CODE_DOES_NOT_APPLY()
 * @method static ErrorCode COUPON_CODE_ALREADY_APPLIED()
 * @method static ErrorCode PRODUCT_NOT_FOUND()
 */
final class ErrorCode extends Enum
{
    private const BASKET_NOT_FOUND = 1;
    private const COUPON_CODE_DOES_NOT_APPLY = 2;
    private const COUPON_CODE_ALREADY_APPLIED = 3;
    private const PRODUCT_NOT_FOUND = 4;
}
