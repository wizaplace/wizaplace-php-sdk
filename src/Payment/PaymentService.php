<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Payment;

use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Basket\Payment;

final class PaymentService extends AbstractService
{
    /** @return Payment[] */
    public function getPaymentMethods(): array
    {
        $this->client->mustBeAuthenticated();

        return \array_map(
            /** @param mixed[] $payment */
            function (array $payment): Payment {
                return new Payment($payment);
            },
            $this->client->get("payments")
        );
    }
}
