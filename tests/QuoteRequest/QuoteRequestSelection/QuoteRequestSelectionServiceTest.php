<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\QuoteRequest\QuoteRequestSelection;

use Wizaplace\SDK\PaginatedData;
use Wizaplace\SDK\QuoteRequest\QuoteRequestSelection\QuoteRequestSelectionFilter;
use Wizaplace\SDK\QuoteRequest\QuoteRequestSelection\QuoteRequestSelectionService;
use Wizaplace\SDK\Tests\ApiTestCase;

class QuoteRequestSelectionServiceTest extends ApiTestCase
{
    public function testListBy(): void
    {
        $service = $this->buildQuoteRequestSelectionService('user@wizaplace.com', static::VALID_PASSWORD);

        // No filter
        $paginatedData = $service->listBy();
        static::assertInstanceOf(PaginatedData::class, $paginatedData);
        static::assertEquals(100, $paginatedData->getLimit());
        static::assertEquals(0, $paginatedData->getOffset());
        static::assertEquals(5, $paginatedData->getTotal());
        static::assertCount($paginatedData->getTotal(), $paginatedData->getItems());

        // Get all selections
        $selectionFilter = (new QuoteRequestSelectionFilter())
            ->setIds([1, 2, 3, 4, 5])
            ->setUserIds([3])
            ->setCreationPeriodFrom(new \DateTime('2021-01-01'))
            ->setCreationPeriodTo(new \DateTime('2022-01-01'));

        $paginatedData = $service->listBy($selectionFilter);
        static::assertInstanceOf(PaginatedData::class, $paginatedData);
        static::assertEquals(100, $paginatedData->getLimit());
        static::assertEquals(0, $paginatedData->getOffset());
        static::assertEquals(5, $paginatedData->getTotal());
        static::assertCount($paginatedData->getTotal(), $paginatedData->getItems());

        // Get selection 3
        $selectionFilter = (new QuoteRequestSelectionFilter())->setIds([3]);
        $paginatedData = $service->listBy($selectionFilter);
        static::assertEquals(1, $paginatedData->getTotal());
        static::assertCount($paginatedData->getTotal(), $paginatedData->getItems());
        static::assertSame(3, $paginatedData->getItems()[0]->getId());

        // Get active selection
        $selectionFilter = (new QuoteRequestSelectionFilter())->setActive(true);
        $paginatedData = $service->listBy($selectionFilter);
        static::assertEquals(1, $paginatedData->getTotal());
        static::assertCount($paginatedData->getTotal(), $paginatedData->getItems());
        static::assertSame(5, $paginatedData->getItems()[0]->getId());

        // Get selection with declination 1_0
        $selectionFilter = (new QuoteRequestSelectionFilter())->setDeclinationIds(['1_0']);
        $paginatedData = $service->listBy($selectionFilter);
        static::assertEquals(1, $paginatedData->getTotal());
        static::assertCount($paginatedData->getTotal(), $paginatedData->getItems());
        static::assertSame(5, $paginatedData->getItems()[0]->getId());

        // Get selection created the 2021-12-23
        $selectionFilter = (new QuoteRequestSelectionFilter())
            ->setCreationPeriodFrom(new \DateTime('2021-12-23T00:00:00+00:00'))
            ->setCreationPeriodTo(new \DateTime('2021-12-23T23:59:59+00:00'));
        $paginatedData = $service->listBy($selectionFilter);
        static::assertEquals(1, $paginatedData->getTotal());
        static::assertCount($paginatedData->getTotal(), $paginatedData->getItems());
        static::assertSame(4, $paginatedData->getItems()[0]->getId());

        // Get selection updated the 2021-12-23
        $selectionFilter = (new QuoteRequestSelectionFilter())
            ->setCreationPeriodFrom(new \DateTime('2021-12-22T00:00:00+00:00'))
            ->setCreationPeriodTo(new \DateTime('2021-12-22T23:59:59+00:00'));
        $paginatedData = $service->listBy($selectionFilter);
        static::assertEquals(1, $paginatedData->getTotal());
        static::assertCount($paginatedData->getTotal(), $paginatedData->getItems());
        static::assertSame(3, $paginatedData->getItems()[0]->getId());

        // Test returned object
        $paginatedData = $service->listBy((new QuoteRequestSelectionFilter())->setLimit(1));
        static::assertInstanceOf(PaginatedData::class, $paginatedData);
        static::assertEquals(1, $paginatedData->getLimit());
        static::assertEquals(0, $paginatedData->getOffset());
        static::assertEquals(5, $paginatedData->getTotal());

        static::assertSame(5, $paginatedData->getItems()[0]->getId());
        static::assertSame(3, $paginatedData->getItems()[0]->getUserId());
        static::assertSame(true, $paginatedData->getItems()[0]->isActive());
        static::assertSame(['1_0', '4_0'], $paginatedData->getItems()[0]->getDeclinationIds());
        static::assertSame([], $paginatedData->getItems()[0]->getQuoteRequestsIds());
        static::assertEquals(new \DateTime('2021-12-24T10:22:20+00:00'), $paginatedData->getItems()[0]->getCreatedAt());
        static::assertEquals(new \DateTime('2021-12-25T12:27:06+00:00'), $paginatedData->getItems()[0]->getUpdatedAt());
    }

    private function buildQuoteRequestSelectionService(string $email, string $password): QuoteRequestSelectionService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new QuoteRequestSelectionService($apiClient);
    }
}
