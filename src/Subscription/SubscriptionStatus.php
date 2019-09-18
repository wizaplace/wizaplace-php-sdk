<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Subscription;

use MyCLabs\Enum\Enum;

/**
 * @method static SubscriptionStatus ACTIVE()
 * @method static SubscriptionStatus DEFAULTED()
 * @method static SubscriptionStatus DISABLED()
 * @method static SubscriptionStatus FINISHED()
 * @method static SubscriptionStatus SUSPENDED()
 */
final class SubscriptionStatus extends Enum
{
    private const ACTIVE = "ACTIVE";
    private const DEFAULTED = "DEFAULTED";
    private const DISABLED = "DISABLED";
    private const FINISHED = "FINISHED";
    private const SUSPENDED = "SUSPENDED";
}
