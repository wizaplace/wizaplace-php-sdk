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

    public static function stringToStatus(string $status): self
    {
        switch ($status) {
            case static::ACTIVE:
                return static::ACTIVE();
            case static::DEFAULTED:
                return static::DEFAULTED();
            case static::DISABLED:
                return static::DISABLED();
            case static::FINISHED:
                return static::FINISHED();
            case static::SUSPENDED:
                return static::SUSPENDED();
            default:
                throw new \UnexpectedValueException("Unknow status '$status''.");
        }
    }
}
