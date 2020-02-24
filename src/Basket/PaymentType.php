<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Basket;

use MyCLabs\Enum\Enum;

/**
 * Class PaymentType
 * @package Wizaplace\SDK\Basket
 *
 * @method static PaymentType CREDIT_CARD()
 * @method static PaymentType BANK_TRANSFER()
 * @method static PaymentType SEPA_DIRECT()
 * @method static PaymentType PAYMENT_DEFERMENT()
 * @method static PaymentType MANUAL()
 * @method static PaymentType NONE()
 */
final class PaymentType extends Enum
{
    private const CREDIT_CARD = 'credit-card';
    private const CREDIT_CARD_CAPTURE = 'credit-card-capture';
    private const BANK_TRANSFER = 'bank-transfer';
    private const SEPA_DIRECT = 'sepa-direct';
    private const PAYMENT_DEFERMENT = 'payment-deferment';
    private const MANUAL = 'manual';
    private const NONE = 'none';
}
