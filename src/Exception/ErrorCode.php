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
 * @method static ErrorCode REVIEWS_ARE_DISABLED()
 * @method static ErrorCode SENDER_IS_ALSO_RECIPIENT()
 * @method static ErrorCode COMPANY_HAS_NO_ADMINISTRATOR()
 * @method static ErrorCode COMPANY_NOT_FOUND()
 */
final class ErrorCode extends Enum
{
    private const BASKET_NOT_FOUND = 1;
    private const COUPON_CODE_DOES_NOT_APPLY = 2;
    private const COUPON_CODE_ALREADY_APPLIED = 3;
    private const PRODUCT_NOT_FOUND = 4;
    private const REVIEWS_ARE_DISABLED = 5;
    private const SENDER_IS_ALSO_RECIPIENT = 6;
    private const COMPANY_HAS_NO_ADMINISTRATOR = 7;
    private const COMPANY_NOT_FOUND = 8;
}
