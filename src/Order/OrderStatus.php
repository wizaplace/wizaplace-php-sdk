<?php
/**
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Order;

use MyCLabs\Enum\Enum;

/**
 * @method static OrderStatus STANDBY_BILLING()
 * @method static OrderStatus STANDBY_VENDOR()
 * @method static OrderStatus PROCESSING_SHIPPING()
 * @method static OrderStatus PROCESSED()
 * @method static OrderStatus COMPLETED()
 * @method static OrderStatus BILLING_FAILED()
 * @method static OrderStatus VENDOR_DECLINED()
 * @method static OrderStatus STANDBY_SUPPLYING()
 * @method static OrderStatus UNPAID()
 * @method static OrderStatus REFUNDED()
 * @method static OrderStatus CANCELED()
 * @method static OrderStatus INCOMPLETED()
 * @method static OrderStatus PARENT_ORDER()
 */
final class OrderStatus extends Enum
{
    private const STANDBY_BILLING = "STANDBY_BILLING";
    private const STANDBY_VENDOR = "STANDBY_VENDOR";
    private const PROCESSING_SHIPPING = "PROCESSING_SHIPPING";
    private const PROCESSED = "PROCESSED";
    private const COMPLETED = "COMPLETED";
    private const BILLING_FAILED = "BILLING_FAILED";
    private const VENDOR_DECLINED = "VENDOR_DECLINED";
    private const STANDBY_SUPPLYING = "STANDBY_SUPPLYING";
    private const UNPAID = "UNPAID";
    private const REFUNDED = "REFUNDED";
    private const CANCELED = "CANCELED";
    private const INCOMPLETED = "INCOMPLETED";
    private const PARENT_ORDER = "PARENT_ORDER";
}
