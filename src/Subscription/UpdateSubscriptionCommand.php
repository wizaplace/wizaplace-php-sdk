<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Subscription;

final class UpdateSubscriptionCommand extends SubscriptionUpsertData
{
    /** @var string */
    private $subscriptionId;

    public function __construct(string $subscriptionId)
    {
        $this->subscriptionId = $subscriptionId;
    }

    public function getSubscriptionId(): string
    {
        return $this->subscriptionId;
    }

    protected static function allowsPartialData(): bool
    {
        return false;
    }
}
