<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Subscription;

use Wizaplace\SDK\Exception\AccessDenied;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\PaginatedData;
use Wizaplace\SDK\Price;
use Wizaplace\SDK\Subscription\Subscription;
use Wizaplace\SDK\Subscription\SubscriptionActionTrace;
use Wizaplace\SDK\Subscription\SubscriptionActionTraceFilter;
use Wizaplace\SDK\Subscription\SubscriptionEventType;
use Wizaplace\SDK\Subscription\SubscriptionItem;
use Wizaplace\SDK\Subscription\SubscriptionService;
use Wizaplace\SDK\Subscription\SubscriptionStatus;
use Wizaplace\SDK\Subscription\SubscriptionSummary;
use Wizaplace\SDK\Subscription\SubscriptionTax;
use Wizaplace\SDK\Subscription\UpdateSubscriptionCommand;
use Wizaplace\SDK\Tests\ApiTestCase;
use Wizaplace\SDK\Vendor\Order\Order as VendorOrder;

class SubscriptionServiceTest extends ApiTestCase
{
    public function testListBy(): void
    {
        $service = $this->buildSubscriptionService();

        static::expectException(AccessDenied::class);
        $service->listBy();

        $service = $this->buildSubscriptionService('admin@wizaplace.com', 'password');
        $paginatedData = $service->listBy();

        static::assertInstanceOf(PaginatedData::class, $paginatedData);
        static::assertEquals(10, $paginatedData->getLimit());
        static::assertEquals(0, $paginatedData->getOffset());
        static::assertEquals(2, $paginatedData->getTotal());
        static::assertCount($paginatedData->getTotal(), $paginatedData->getItems());

        /** @var SubscriptionSummary[] $items */
        $items = $paginatedData->getItems();

        foreach ($items as $key => $item) {
            static::assertInstanceOf(SubscriptionSummary::class, $item);
            static::assertUuid($item->getId());
            static::greaterThan(0)->evaluate($item->getUserId());
            static::greaterThan(0)->evaluate($item->getCompanyId());

            if (0 === $key) {
                static::assertUuid($item->getCardId());
            } else {
                static::assertNull($item->getCardId());
            }

            static::assertInstanceOf(SubscriptionStatus::class, $item->getStatus());
            static::assertInstanceOf(Price::class, $item->getPrice());
            static::greaterThan(0)->evaluate($item->getFirstOrderId());
            static::greaterThan(0)->evaluate($item->getCommitmentPeriod());
            static::greaterThan(0)->evaluate($item->getPaymentFrequency());
            static::assertInstanceOf(\DateTime::class, $item->getCommitmentEndAt());
            static::assertInstanceOf(\DateTime::class, $item->getNextPaymentAt());
            static::assertInstanceOf(\DateTime::class, $item->getCreatedAt());
        }
    }

    public function testGetSubscription(): void
    {
        $service = $this->buildSubscriptionService();

        static::expectException(NotFound::class);
        $service->getSubscription("2fe16ba9-42b7-4028-908f-677dce2f2b2d");

        $service = $this->buildSubscriptionService('admin@wizaplace.com', 'password');
        $subscription = $service->getSubscription("2fe16ba9-42b7-4028-908f-677dce2f2b2d");

        static::assertInstanceOf(Subscription::class, $subscription);
        static::assertUuid($subscription->getId());
        static::assertSame("2fe16ba9-42b7-4028-908f-677dce2f2b2d", $subscription->getId());
        static::greaterThan(0)->evaluate($subscription->getUserId());
        static::greaterThan(0)->evaluate($subscription->getCompanyId());
        static::assertUuid($subscription->getCardId());
        static::assertInstanceOf(SubscriptionStatus::class, $subscription->getStatus());
        static::assertInstanceOf(Price::class, $subscription->getPrice());
        static::greaterThan(0)->evaluate($subscription->getFirstOrderId());
        static::greaterThan(0)->evaluate($subscription->getCommitmentPeriod());
        static::greaterThan(0)->evaluate($subscription->getPaymentFrequency());
        static::assertInstanceOf(\DateTime::class, $subscription->getCommitmentEndAt());
        static::assertInstanceOf(\DateTime::class, $subscription->getNextPaymentAt());
        static::assertInstanceOf(\DateTime::class, $subscription->getCreatedAt());
        static::assertCount(1, $subscription->getItems());
        static::assertCount(1, $subscription->getTaxes());

        foreach ($subscription->getItems() as $item) {
            static::assertInstanceOf(SubscriptionItem::class, $item);
            static::greaterThan(0)->evaluate($item->getCategoryId());
            static::greaterThan(0)->evaluate($item->getProductId());
            static::assertInstanceOf(Price::class, $item->getUnitPrice());
            static::assertInstanceOf(Price::class, $item->getTotalPrice());
        }

        foreach ($subscription->getTaxes() as $tax) {
            static::assertInstanceOf(SubscriptionTax::class, $tax);
            static::greaterThan(0)->evaluate($tax->getTaxId());
            static::greaterThan(0)->evaluate($tax->getAmount());
        }
    }

    public function testGetItems(): void
    {
        $service = $this->buildSubscriptionService('admin@wizaplace.com', 'password');
        $paginatedData = $service->getItems("2fe16ba9-42b7-4028-908f-677dce2f2b2d");

        static::assertInstanceOf(PaginatedData::class, $paginatedData);
        static::assertEquals(10, $paginatedData->getLimit());
        static::assertEquals(0, $paginatedData->getOffset());
        static::assertEquals(1, $paginatedData->getTotal());
        static::assertCount($paginatedData->getTotal(), $paginatedData->getItems());

        /** @var SubscriptionItem[] $items */
        $items = $paginatedData->getItems();

        foreach ($items as $key => $item) {
            static::assertInstanceOf(SubscriptionItem::class, $item);
            static::greaterThan(0)->evaluate($item->getCategoryId());
            static::greaterThan(0)->evaluate($item->getProductId());
            static::assertInstanceOf(Price::class, $item->getUnitPrice());
            static::assertInstanceOf(Price::class, $item->getTotalPrice());
        }
    }

    public function testGetTaxes(): void
    {
        $service = $this->buildSubscriptionService('admin@wizaplace.com', 'password');
        $paginatedData = $service->getTaxes("2fe16ba9-42b7-4028-908f-677dce2f2b2d");

        static::assertInstanceOf(PaginatedData::class, $paginatedData);
        static::assertEquals(10, $paginatedData->getLimit());
        static::assertEquals(0, $paginatedData->getOffset());
        static::assertEquals(1, $paginatedData->getTotal());
        static::assertCount($paginatedData->getTotal(), $paginatedData->getItems());

        /** @var SubscriptionTax[] $items */
        $items = $paginatedData->getItems();

        foreach ($items as $key => $tax) {
            static::assertInstanceOf(SubscriptionTax::class, $tax);
            static::greaterThan(0)->evaluate($tax->getTaxId());
            static::greaterThan(0)->evaluate($tax->getAmount());
        }
    }

    public function testGetOrders(): void
    {
        $service = $this->buildSubscriptionService('admin@wizaplace.com', 'password');
        $paginatedData = $service->getOrders("2fe16ba9-42b7-4028-908f-677dce2f2b2d");

        static::assertInstanceOf(PaginatedData::class, $paginatedData);
        static::assertEquals(10, $paginatedData->getLimit());
        static::assertEquals(0, $paginatedData->getOffset());
        static::assertEquals(1, $paginatedData->getTotal());
        static::assertCount($paginatedData->getTotal(), $paginatedData->getItems());

        /** @var VendorOrder[] $items */
        $items = $paginatedData->getItems();

        foreach ($items as $key => $order) {
            static::assertInstanceOf(VendorOrder::class, $order);
            static::assertUuid($order->getSubscriptionId());
            static::assertSame("2fe16ba9-42b7-4028-908f-677dce2f2b2d", $order->getSubscriptionId());
            static::assertFalse($order->isSubscriptionInitiator());
        }
    }

    public function testPatchASubscription(): void
    {
        $service = $this->buildSubscriptionService('admin@wizaplace.com', static::VALID_PASSWORD);

        $updateSubscriptionCommand = (new UpdateSubscriptionCommand('0f380a36-0188-4133-89b9-40fe36e7d47b'))
            ->setStatus(SubscriptionStatus::ACTIVE())
            ->setIsAutorenew(true)
            ->setNextPaymentAt($nextPaymentAt = (new \DateTime('2153-01-01'))->format('Y-m-d'))
            ->setCommitmentEndAt($commitmentEndAt = (new \DateTime('2153-02-01'))->format('Y-m-d'))
            ->setQuantity(12)
        ;

        $subscription = $service->patchSubscription($updateSubscriptionCommand);

        static::assertInstanceOf(Subscription::class, $subscription);
        static::assertUuid($subscription->getId());
        static::assertSame('0f380a36-0188-4133-89b9-40fe36e7d47b', $subscription->getId());
        static::assertInstanceOf(SubscriptionStatus::class, $subscription->getStatus());
        static::assertSame(SubscriptionStatus::ACTIVE()->getValue(), $subscription->getStatus()->getValue());
        static::assertTrue($subscription->isAutorenew());
        static::assertSame(12, $subscription->getItems()[0]->getQuantity());
        static::assertEquals(new \DateTime($nextPaymentAt), $subscription->getNextPaymentAt());
        static::assertEquals(new \DateTime($commitmentEndAt), $subscription->getCommitmentEndAt());
    }

    public function testGetLogsSubscriptionOnSubscriptionCreated(): void
    {
        $service = $this->buildSubscriptionService('admin@wizaplace.com', 'Windows.98');
        $subscriptionId = '85e8f81f-3f14-4036-a9fd-dd65e52174af';
        $paginatedData = $service->getSubscriptionActionTrace($subscriptionId);

        static::assertInstanceOf(PaginatedData::class, $paginatedData);
        static::assertSame(10, $paginatedData->getLimit());
        static::assertSame(0, $paginatedData->getOffset());
        static::assertSame(2, $paginatedData->getTotal());
        static::assertCount($paginatedData->getTotal(), $paginatedData->getItems());

        /** @var SubscriptionActionTrace[] $items */
        $items = $paginatedData->getItems();

        foreach ($items as $key => $subscriptionLog) {
            static::assertInstanceOf(SubscriptionActionTrace::class, $subscriptionLog);
            static::assertUuid($subscriptionLog->getSubscriptionId());
            static::assertSame($subscriptionId, $subscriptionLog->getSubscriptionId());
            static::assertNotNull($subscriptionId, $subscriptionLog->getSubscriptionId());
            static::assertInstanceOf(\DateTime::class, $subscriptionLog->getDate());
            static::assertNotNull($subscriptionLog->getDate());
        }

        $secondRowOfSubscriptionLog = \reset($items);
        static::assertSame(SubscriptionEventType::STATUS_UPDATED()->getValue(), $secondRowOfSubscriptionLog->getAction()->getValue());
        static::assertSame(2, $secondRowOfSubscriptionLog->getUserId());
        static::assertSame('DISABLED', $secondRowOfSubscriptionLog->getValueBefore());
        static::assertSame('ACTIVE', $secondRowOfSubscriptionLog->getValueAfter());

        $firstRowSubscriptionLog = \end($items);
        static::assertSame(SubscriptionEventType::SUBSCRIPTION_CREATED()->getValue(), $firstRowSubscriptionLog->getAction()->getValue());
        static::assertSame(0, $firstRowSubscriptionLog->getUserId());
        static::assertNull($firstRowSubscriptionLog->getValueBefore());
        static::assertNull($firstRowSubscriptionLog->getValueAfter());
    }

    public function testGetSubscriptionLogsWithFilterLimit(): void
    {
        $service = $this->buildSubscriptionService('admin@wizaplace.com', 'Windows.98');
        $subscriptionId = '85e8f81f-3f14-4036-a9fd-dd65e52174af';
        $paginatedData = $service->getSubscriptionActionTrace($subscriptionId);

        static::assertInstanceOf(PaginatedData::class, $paginatedData);
        static::assertSame(10, $paginatedData->getLimit());
        static::assertSame(0, $paginatedData->getOffset());
        static::assertSame(3, $paginatedData->getTotal());
        static::assertCount($paginatedData->getTotal(), $paginatedData->getItems());

        /** @var SubscriptionActionTrace[] $items */
        $items = $paginatedData->getItems();

        // Le résultat est affiché dans l'ordre décroissant
        $lastSubscriptionLog = \reset($items);

        static::assertSame(SubscriptionEventType::STATUS_UPDATED()->getValue(), $lastSubscriptionLog->getAction()->getValue());
        static::assertSame(2, $lastSubscriptionLog->getUserId());
        static::assertSame('ACTIVE', $lastSubscriptionLog->getValueBefore());
        static::assertSame('DEFAULTED', $lastSubscriptionLog->getValueAfter());
        static::assertInstanceOf(\DateTime::class, $lastSubscriptionLog->getDate());

        $filter = (new SubscriptionActionTraceFilter())
            ->setLimit(1)
            ->setOffset(2);

        $paginatedDataWithLimitAndOffset = $service->getSubscriptionActionTrace($subscriptionId, $filter);

        static::assertInstanceOf(PaginatedData::class, $paginatedData);
        static::assertSame($filter->getLimit(), $paginatedDataWithLimitAndOffset->getLimit());
        static::assertSame($filter->getOffset(), $paginatedDataWithLimitAndOffset->getOffset());
        static::assertSame(3, $paginatedDataWithLimitAndOffset->getTotal());

        /** @var SubscriptionActionTrace[] $items */
        $items = $paginatedDataWithLimitAndOffset->getItems();

        $subscriptionLogWithFilter = \reset($items);

        static::assertSame(SubscriptionEventType::SUBSCRIPTION_CREATED()->getValue(), $subscriptionLogWithFilter->getAction()->getValue());
        static::assertSame(0, $subscriptionLogWithFilter->getUserId());
        static::assertNull($subscriptionLogWithFilter->getValueBefore());
        static::assertNull($subscriptionLogWithFilter->getValueAfter());
        static::assertInstanceOf(\DateTime::class, $subscriptionLogWithFilter->getDate());
    }

    private function buildSubscriptionService(string $email = 'user@wizaplace.com', string $password = 'password'): SubscriptionService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new SubscriptionService($apiClient);
    }
}
