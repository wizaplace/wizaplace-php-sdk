<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Order;

use MyCLabs\Enum\Enum;

/**
 * @method static RefundPaymentMethod REFUND_CB()
 * @method static RefundPaymentMethod MARK_AS_PAID()
 * @method static RefundPaymentMethod PAY_LATER()
 */
class RefundPaymentMethod extends Enum
{
    protected const REFUND_CB = 'refund_cb';
    protected const MARK_AS_PAID = 'mark_refund_as_paid';
    protected const PAY_LATER = 'pay_later';
}
