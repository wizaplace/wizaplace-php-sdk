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
 * @method static RefundStatus CREATED()
 * @method static RefundStatus PAID()
 * @method static RefundStatus FAILED()
 * @method static RefundStatus MARKED_AS_PAID()
 */
final class RefundStatus extends Enum
{
    private const CREATED = 'created';
    private const PAID = 'paid';
    private const FAILED = 'failed';
    private const MARKED_AS_PAID = 'marked_as_paid';
}
