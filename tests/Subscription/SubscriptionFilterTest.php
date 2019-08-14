<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Subscription;

use PHPUnit\Framework\TestCase;
use Wizaplace\SDK\Subscription\SubscriptionFilter;
use Wizaplace\SDK\Subscription\SubscriptionStatus;

class SubscriptionFilterTest extends TestCase
{
    public function testGetFilters(): void
    {
        $subscriptionFilter = new SubscriptionFilter();

        $filters = $subscriptionFilter->getFilters();
        static::assertCount(0, $filters);

        $before = new \DateTime("2019-08-13");
        $after = new \DateTime("2019-08-01");

        $subscriptionFilter
            ->setLimit(1)
            ->setOffset(2)
            ->setStatus(SubscriptionStatus::ACTIVE())
            ->setCompanyId(3)
            ->setUserId(4)
            ->setProductId(5)
            ->setCommitmentEndBefore($before)
            ->setCommitmentEndAfter($after)
            ->setIsAutorenew(true);
        $filters = $subscriptionFilter->getFilters();
        static::assertCount(9, $filters);
        static::assertSame(1, $filters[SubscriptionFilter::LIMIT]);
        static::assertSame(2, $filters[SubscriptionFilter::OFFSET]);
        static::assertSame(SubscriptionStatus::ACTIVE()->getValue(), $filters[SubscriptionFilter::STATUS]);
        static::assertSame(3, $filters[SubscriptionFilter::COMPANY_ID]);
        static::assertSame(4, $filters[SubscriptionFilter::USER_ID]);
        static::assertSame(5, $filters[SubscriptionFilter::PRODUCT_ID]);
        static::assertSame($before->format(\DateTime::RFC3339), $filters[SubscriptionFilter::COMMITMENT_END_BEFORE]);
        static::assertSame($after->format(\DateTime::RFC3339), $filters[SubscriptionFilter::COMMITMENT_END_AFTER]);
        static::asserttrue($filters[SubscriptionFilter::IS_AUTORENEW]);
    }
}
