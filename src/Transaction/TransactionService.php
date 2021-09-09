<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Transaction;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Exception\AccessDenied;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;
use Wizaplace\SDK\PaginatedData;
use Wizaplace\SDK\Pagination;

class TransactionService extends AbstractService
{
    /** @var Pagination */
    private $pagination;

    // Sets the number of items to display on a single page
    public const MAX_ITEM_PER_PAGE = 10;

    public function getListTransaction(
        TransactionFilter $transactionFilter = null
    ): TransactionList {
        $this->client->mustBeAuthenticated();

        if ($transactionFilter instanceof TransactionFilter === false) {
            $transactionFilter = (new TransactionFilter())
                ->setPage(1)
                ->setResultPerPage(self::MAX_ITEM_PER_PAGE);
        }

        try {
            $data = $this->client->get(
                'reports/transactions',
                [RequestOptions::QUERY => $transactionFilter->getFilters()]
            );
        } catch (ClientException $exception) {
            if (400 === $exception->getResponse()->getStatusCode()) {
                throw new SomeParametersAreInvalid('Some parameters are invalid.', 400, $exception);
            }

            if (403 === $exception->getResponse()->getStatusCode()) {
                throw new AccessDenied('You\'re not allowed to access to this endpoint.', 403, $exception);
            }

            throw $exception;
        }

        return new TransactionList($data);
    }
}
