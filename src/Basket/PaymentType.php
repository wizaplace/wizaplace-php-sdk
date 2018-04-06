<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Basket;

use MyCLabs\Enum\Enum;

/**
 * @method static PaymentType CREDIT_CARD()
 * @method static PaymentType BANK_TRANSFER()
 * @method static PaymentType PAYMENT_DEFERMENT()
 * @method static PaymentType MANUAL()
 * @method static PaymentType NONE()
 */
final class PaymentType extends Enum
{
    private const CREDIT_CARD = 'credit-card';
    private const BANK_TRANSFER = 'bank-transfer';
    private const PAYMENT_DEFERMENT = 'payment-deferment';
    private const MANUAL = 'manual';
    private const NONE = 'none';
}
