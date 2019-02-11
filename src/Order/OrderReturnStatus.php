<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Order;

use MyCLabs\Enum\Enum;

/**
 * Class OrderReturn
 * @package Wizaplace\SDK\Order
 *
 * @method static OrderReturnStatus PROCESSING()
 * @method static OrderReturnStatus RECEIVED()
 * @method static OrderReturnStatus COMPLETED()
 * @method static OrderReturnStatus DECLINED_BY_VENDOR()
 */
final class OrderReturnStatus extends Enum
{
    private const PROCESSING = 'R';
    private const RECEIVED = 'A';
    private const COMPLETED = 'C';
    private const DECLINED_BY_VENDOR = 'D';
}
