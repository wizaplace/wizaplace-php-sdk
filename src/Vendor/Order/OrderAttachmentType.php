<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Order;

use MyCLabs\Enum\Enum;

/**
 * @method static OrderAttachmentType DELIVERY_BILL()
 * @method static OrderAttachmentType CUSTOMER_INVOICE()
 * @method static OrderAttachmentType OTHER()
 */
class OrderAttachmentType extends Enum
{
    private const DELIVERY_BILL = 'DELIVERY_BILL';
    private const CUSTOMER_INVOICE = 'CUSTOMER_INVOICE';
    private const OTHER = 'OTHER';
}
