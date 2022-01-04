<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\QuoteRequest\QuoteRequestSelection;

use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\PaginatedData;
use Wizaplace\SDK\QuoteRequest\QuoteRequestSelection\QuoteRequestSelectionDeclination;
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

    public function testAddDeclinationToSelection(): void
    {
        $service = $this->buildQuoteRequestSelectionService('user@wizaplace.com', static::VALID_PASSWORD);

        $selectionFilter = (new QuoteRequestSelectionFilter())->setActive(true);
        $paginatedData = $service->listBy($selectionFilter);
        $selectionId = $paginatedData->getItems()[0]->getId();

        $declinationToAdd = [
            new QuoteRequestSelectionDeclination('1_0', 1),
            new QuoteRequestSelectionDeclination('4_0', 3),
        ];
        static::assertSame([
            'declinations' => [
                ['declinationId' => '1_0', 'quantity' => 2, 'added' => 1],
                ['declinationId' => '4_0', 'quantity' => 8, 'added' => 3]
            ]
        ], $service->addDeclinationToSelection($declinationToAdd, $selectionId));

        // Add to current active selection
        static::assertSame([
            'declinations' => [
                ['declinationId' => '1_0', 'quantity' => 3, 'added' => 1],
                ['declinationId' => '4_0', 'quantity' => 11, 'added' => 3]
            ]
        ], $service->addDeclinationToSelection($declinationToAdd));

        // Selection doesn't exist
        static::expectException(NotFound::class);
        $declinationToAdd = [new QuoteRequestSelectionDeclination('4_0', 1)];
        $service->addDeclinationToSelection($declinationToAdd, 15);
    }

    public function testUpdateSelectionDeclinations(): void
    {
        $service = $this->buildQuoteRequestSelectionService('user@wizaplace.com', static::VALID_PASSWORD);

        $selectionFilter = (new QuoteRequestSelectionFilter())->setActive(true);
        $paginatedData = $service->listBy($selectionFilter);
        $selectionId = $paginatedData->getItems()[0]->getId();

        $declinationToUpdate = [
            new QuoteRequestSelectionDeclination('1_0', 1),
            new QuoteRequestSelectionDeclination('4_0', 3),
        ];
        static::assertSame([
            'declinations' => [
                ['declinationId' => '1_0', 'quantity' => 1],
                ['declinationId' => '4_0', 'quantity' => 3]
            ]
        ], $service->updateSelectionDeclinations($declinationToUpdate, $selectionId));

        // Update current active selection
        $declinationToUpdate = [
            new QuoteRequestSelectionDeclination('1_0', 2),
            new QuoteRequestSelectionDeclination('4_0', 4),
        ];
        static::assertSame([
            'declinations' => [
                ['declinationId' => '1_0', 'quantity' => 2],
                ['declinationId' => '4_0', 'quantity' => 4]
            ]
        ], $service->updateSelectionDeclinations($declinationToUpdate));

        // Selection doesn't exist
        static::expectException(NotFound::class);
        $declinationToUpdate = [new QuoteRequestSelectionDeclination('4_0', 1)];
        $service->updateSelectionDeclinations($declinationToUpdate, 15);
    }

    public function testRemoveDeclinationFromSelection(): void
    {
        $service = $this->buildQuoteRequestSelectionService('user@wizaplace.com', static::VALID_PASSWORD);

        $selectionFilter = (new QuoteRequestSelectionFilter())->setActive(true);
        $paginatedData = $service->listBy($selectionFilter);
        $selectionId = $paginatedData->getItems()[0]->getId();

        $declinationToAdd = [
            new QuoteRequestSelectionDeclination('1_0', 1),
            new QuoteRequestSelectionDeclination('4_0', 3),
        ];
        static::assertSame([
            'declinations' => [
                ['declinationId' => '1_0', 'quantity' => 2, 'added' => 1],
                ['declinationId' => '4_0', 'quantity' => 8, 'added' => 3]
            ]
        ], $service->addDeclinationToSelection($declinationToAdd, $selectionId));
        static::assertSame(
            [['declinationId' => '4_0']],
            $service->removeDeclinationFromSelection(['4_0'], $selectionId)
        );

        // Add and remove from current active selection
        static::assertSame([
            'declinations' => [
                ['declinationId' => '1_0', 'quantity' => 2, 'added' => 1],
                ['declinationId' => '4_0', 'quantity' => 8, 'added' => 3]
            ]
        ], $service->addDeclinationToSelection($declinationToAdd));
        static::assertSame(
            [['declinationId' => '4_0']],
            $service->removeDeclinationFromSelection(['4_0']));

        // Declination already removed
        static::expectException(NotFound::class);
        $service->removeDeclinationFromSelection(['4_0'], $selectionId);
    }

    private function buildQuoteRequestSelectionService(string $email, string $password): QuoteRequestSelectionService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new QuoteRequestSelectionService($apiClient);
    }
}
