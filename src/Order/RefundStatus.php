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
 */
final class RefundStatus extends Enum
{
    private const CREATED = 'created';
    private const PAID = 'paid';
    private const FAILED = 'failed';
}
