<?php
/**
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\OrderReturn;

use MyCLabs\Enum\Enum;

/**
 * @method static ReturnStatus PROCESSING()
 * @method static ReturnStatus RECEIVED()
 * @method static ReturnStatus COMPLETED()
 * @method static ReturnStatus DECLINED_BY_VENDOR()
 */
final class ReturnStatus extends Enum
{
    private const PROCESSING = 'R';
    private const RECEIVED = 'A';
    private const COMPLETED = 'C';
    private const DECLINED_BY_VENDOR = 'D';
}
