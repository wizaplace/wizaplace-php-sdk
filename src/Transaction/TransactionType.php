<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Transaction;

use MyCLabs\Enum\Enum;

/**
 * Manage type of transaction
 *
 * @method static TransactionType CREDITCARD()
 * @method static TransactionType TRANSFER()
 * @method static TransactionType REFUND()
 */
class TransactionType extends Enum
{
    const CREDITCARD = 'CREDITCARD';
    const TRANSFER = 'TRANSFER';
    const REFUND = 'REFUND';
}
