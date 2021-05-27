<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Transaction;

use Wizaplace\SDK\Pagination;

class TransactionList
{
    /** @var Transaction[] */
    private $transactions;

    /** @var Pagination */
    private $pagination;

    public function __construct(array $data)
    {
        $this->transactions = \array_map(
            static function (array $item): Transaction {
                return new Transaction($item);
            },
            \reset($data)['results']
        );

        $this->pagination = new Pagination(
            \end($data)
        );
    }

    /**
     * @return Transaction[]
     */
    public function getTransactions(): array
    {
        return $this->transactions;
    }

    public function getPagination(): Pagination
    {
        return $this->pagination;
    }
}
