<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Subscription;

use MyCLabs\Enum\Enum;

/**
 * @method static SubscriptionEventType SUBSCRIPTION_CREATED()
 * @method static SubscriptionEventType STATUS_UPDATED()
 * @method static SubscriptionEventType AUTO_RENEW_UPDATED()
 * @method static SubscriptionEventType PAYMENT_METHOD_RENEWED()
 */
class SubscriptionEventType extends Enum
{
    public const SUBSCRIPTION_CREATED = 'subscription_created';
    public const STATUS_UPDATED = 'status_updated';
    public const AUTO_RENEW_UPDATED = 'auto_renew_updated';
    public const PAYMENT_METHOD_RENEWED = 'payment_method_renewed';
}
