<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Order;

use MyCLabs\Enum\Enum;

/**
 * @method static OrderStatus STANDBY_VENDOR()
 * @method static OrderStatus PROCESSING_SHIPPING()
 * @method static OrderStatus PROCESSED()
 * @method static OrderStatus COMPLETED()
 * @method static OrderStatus VENDOR_DECLINED()
 * @method static OrderStatus STANDBY_SUPPLYING()
 * @method static OrderStatus REFUNDED()
 */
final class OrderStatus extends Enum
{
    /**
     * The order is waiting for the vendor to process it.
     */
    private const STANDBY_VENDOR = 'P';

    /**
     * The order has been shipped but not received yet.
     */
    private const PROCESSING_SHIPPING = 'E';

    /**
     * The order has been shipped/processed, the client can still ask for a refund.
     */
    private const PROCESSED = 'C';

    /**
     * The order is finished and the client cannot ask for a refund anymore.
     */
    private const COMPLETED = 'H';

    /**
     * The vendor declined the order.
     */
    private const VENDOR_DECLINED = 'D';

    /**
     * The order is in back-order (some products need to be ordered first be the vendor).
     */
    private const STANDBY_SUPPLYING = 'B';

    private const REFUNDED = 'A';

    private const UNPAID = 'G';

    private const CANCELED = 'I';
}
