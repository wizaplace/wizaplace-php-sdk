<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Subscription;

 use Wizacha\Marketplace\Payment\PaymentProcessorName;
 use Wizaplace\SDK\Exception\AccessDenied;
use Wizaplace\SDK\PaginatedData;
use Wizaplace\SDK\Pagination;
use Wizaplace\SDK\Price;
use Wizaplace\SDK\Subscription\SubscriptionStatus;
use Wizaplace\SDK\Subscription\SubscriptionSummary;
use Wizaplace\SDK\Tests\ApiTestCase;
use Wizaplace\SDK\Transaction\Transaction;
use Wizaplace\SDK\Transaction\TransactionFilter;
 use Wizaplace\SDK\Transaction\TransactionPeriod;
 use Wizaplace\SDK\Transaction\TransactionService;
use Wizaplace\SDK\Transaction\TransactionStatus;
use Wizaplace\SDK\Transaction\TransactionType;

class TransactionServiceTest extends ApiTestCase
{
    public function testGetTransactionsByAnUnauthorizedUser(): void
    {
        $service = $this->buildTransactionService();

        static::expectException(AccessDenied::class);

        $service->getListTransaction();
    }

    public function testGetListTransactionData(): void
    {
        $service = $this->buildTransactionService('admin@wizaplace.com', 'Windows.98');

        $transactions = $service->getListTransaction();

        $transactionsData = $transactions->getTransactions();
        static::assertGreaterThan(1, $transactionsData);

        $lastTransaction = \end($transactionsData);

        static::assertInstanceOf(\DateTime::class, $lastTransaction->getCreatedAt());
        static::assertNotNull($lastTransaction->getCreatedAt());

        static::assertSame('76dc4bcd-a671-11eb-a758-0242ac120006', $lastTransaction->getId());
        static::assertSame(6.0, $lastTransaction->getAmount());
        static::assertSame(TransactionType::DISPATCH_FUNDS_TRANSFER_COMMISSION()->getValue(), $lastTransaction->getType()->getValue());
        static::assertSame(TransactionStatus::FAILED()->getValue(), $lastTransaction->getStatus()->getValue());
        static::assertSame('107329734', $lastTransaction->getOrigin());
        static::assertSame('107328335', $lastTransaction->getDestination());
        static::assertSame('EUR', $lastTransaction->getCurrency());
        static::assertSame(15, $lastTransaction->getOrderId());
    }

    public function testGetListTransaction(): void
    {
        $service = $this->buildTransactionService('admin@wizaplace.com', 'Windows.98');

        $transactions = $service->getListTransaction();
        $paginationData = $transactions->getPagination();

        static::assertSame(3, $paginationData->getNbResults());
        static::assertSame(1, $paginationData->getNbPages());
        static::assertSame(1, $paginationData->getPage());
        static::assertSame(TransactionService::MAX_ITEM_PER_PAGE, $paginationData->getResultsPerPage());

        $transactionsData = $transactions->getTransactions();
        static::assertCount(3, $transactionsData);

        foreach ($transactionsData as $transaction) {
            static::assertInstanceOf(Transaction::class, $transaction);
        }
    }

    public function testGetListTransactionWithPagination(): void
    {
        $service = $this->buildTransactionService('admin@wizaplace.com', 'Windows.98');

        $transactions = $service->getListTransaction();

        $paginationData = $transactions->getPagination();

        static::assertSame(3, $paginationData->getNbResults());
        static::assertSame(1, $paginationData->getNbPages());
        static::assertSame(1, $paginationData->getPage());
        static::assertSame(TransactionService::MAX_ITEM_PER_PAGE, $paginationData->getResultsPerPage());

        $transactionsData = $transactions->getTransactions();
        static::assertCount(3, $transactionsData);

        foreach ($transactionsData as $transaction) {
            static::assertInstanceOf(Transaction::class, $transaction);
        }

        $lastTransaction = \end($transactionsData);

        // Filter
        $page = $nbResultPerPage = 2;
        $transactionFilter = (new TransactionFilter())
            ->setResultPerPage($nbResultPerPage)
            ->setPage($page)
        ;

        $transactions = $service->getListTransaction($transactionFilter);

        $paginationData = $transactions->getPagination();

        static::assertSame(1, $paginationData->getNbResults());
        static::assertSame(2, $paginationData->getNbPages());
        static::assertSame($page, $paginationData->getPage());
        static::assertSame($nbResultPerPage, $paginationData->getResultsPerPage());

        $transactionsData = $transactions->getTransactions();

        $foundedTransaction = \reset($transactionsData);

        static::assertSame(
            $foundedTransaction->getId(),
            $lastTransaction->getId()
        );
    }

    public function testGetListTransactionWithFilterStatus(): void
    {
        $service = $this->buildTransactionService('admin@wizaplace.com', 'Windows.98');

        $transactions = $service->getListTransaction();

        $transactionsData = $transactions->getTransactions();
        static::assertCount(4, $transactionsData);

        $transactionStatus = TransactionStatus::READY();

        // Count transaction wih failed status
        $nbResultWithReadyStatus = \count(
            \array_filter(
                $transactionsData,
                static function (Transaction $transactionData) use ($transactionStatus) {
                    return  $transactionStatus->getValue() === $transactionData->getStatus()->getValue();
                }
            )
        );

        static::assertSame(1, $nbResultWithReadyStatus);

        // Filter
        $transactionFilter = (new TransactionFilter())
            ->setStatus($transactionStatus)
        ;

        $transactionsWithFilter = $service->getListTransaction($transactionFilter);

        $transactionsData = $transactionsWithFilter->getTransactions();

        static::assertCount(
            $nbResultWithReadyStatus,
            $transactionsData
        );
    }

    public function testGetListTransactionWithFilterTypeOfTransaction(): void
    {
        $service = $this->buildTransactionService('admin@wizaplace.com', 'Windows.98');

        $transactions = $service->getListTransaction();

        $transactionsData = $transactions->getTransactions();
        static::assertCount(4, $transactionsData);

        $transactionType = TransactionType::DISPATCH_FUNDS_TRANSFER_COMMISSION();

        // Count transaction wih failed status
        $nbResultHasTransferCommissionAsType = \count(
            \array_filter(
                $transactionsData,
                static function (Transaction $transactionData) use ($transactionType) {
                    return  $transactionType->getValue() === $transactionData->getType()->getValue();
                }
            )
        );

        static::assertSame(1, $nbResultHasTransferCommissionAsType);

        // Filter
        $transactionFilter = (new TransactionFilter())
            ->setType($transactionType)
        ;

        $transactionsWithFilter = $service->getListTransaction($transactionFilter);

        $transactionsData = $transactionsWithFilter->getTransactions();

        static::assertCount(
            $nbResultHasTransferCommissionAsType,
            $transactionsData
        );
    }

    public function testGetListTransactionWithFilterPeriod(): void
    {
        $service = $this->buildTransactionService('admin@wizaplace.com', 'Windows.98');

        $transactions = $service->getListTransaction();

        $transactionsData = $transactions->getTransactions();
        static::assertCount(3, $transactionsData);

        // Transaction created AT 2021-04-24
        static::assertSame(
            '2021-04-24T11:25:20+02:00',
            $transactionsData[0]->getCreatedAt()->format(DATE_RFC3339)
        );
        // Transaction created AT 2021-04-25
        static::assertSame(
            '2021-04-25T11:26:22+02:00',
            $transactionsData[1]->getCreatedAt()->format(DATE_RFC3339)
        );
        // Transaction created AT 2021-04-26
        static::assertSame(
            '2021-04-26T11:26:22+02:00',
            $transactionsData[2]->getCreatedAt()->format(DATE_RFC3339)
        );

        $transactionPeriod =  (new TransactionPeriod())
            ->setFrom(new \DateTimeImmutable('2021-04-23T11:25:20+02'))
            ->setTo(new \DateTimeImmutable('2021-04-25T11:26:22+02:00'))
        ;

        // Filter
        $transactionFilter = (new TransactionFilter())
            ->setPeriod($transactionPeriod)
        ;

        $transactionsWithFilter = $service->getListTransaction($transactionFilter);

        static::assertCount(
            2,
            $transactionsWithFilter->getTransactions()
        );
    }

    private function buildTransactionService(
        string $email = 'user@wizaplace.com',
        string $password = 'password'
    ): TransactionService {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new TransactionService($apiClient);
    }
}
