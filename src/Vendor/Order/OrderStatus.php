<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Order;

use MyCLabs\Enum\Enum;

/**
 * Class OrderStatus
 * @package Wizaplace\SDK\Vendor\Order
 *
 * @method static OrderStatus STANDBY_BILLING()
 * @method static OrderStatus STANDBY_VENDOR()
 * @method static OrderStatus PROCESSING_SHIPPING()
 * @method static OrderStatus PROCESSED()
 * @method static OrderStatus COMPLETED()
 * @method static OrderStatus BILLING_FAILED()
 * @method static OrderStatus VENDOR_DECLINED()
 * @method static OrderStatus STANDBY_SUPPLYING()
 * @method static OrderStatus REFUNDED()
 * @method static OrderStatus UNPAID()
 * @method static OrderStatus CANCELED()
 * @method static OrderStatus INCOMPLETED()
 */
final class OrderStatus extends Enum
{
    /**
     * The order is waiting to be payed.
     */
    private const STANDBY_BILLING = 'O';

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
     * The payment from the client failed.
     */
    private const BILLING_FAILED = 'F';

    /**
     * The vendor declined the order.
     */
    private const VENDOR_DECLINED = 'D';

    /**
     * The order is in back-order (some products need to be ordered first be the vendor).
     */
    private const STANDBY_SUPPLYING = 'B';

    /**
     * The order has been marked as refunded
     */
    private const REFUNDED = 'A';

    /**
     * The order has been marked as unpaid
     */
    private const UNPAID = 'G';

    /**
     * Canceled by the client.
     */
    private const CANCELED = 'I';

    /**
     * The checkout was started but not completed.
     */
    private const INCOMPLETED = 'N';
}
