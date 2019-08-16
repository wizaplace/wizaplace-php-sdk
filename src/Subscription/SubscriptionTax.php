<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Subscription;

final class SubscriptionTax
{
    /** @var int */
    private $taxId;

    /** @var string */
    private $taxName;

    /** @var float */
    private $amount;

    public function __construct(array $data)
    {
        $this->taxId = $data["taxId"];
        $this->taxName = $data["taxName"];
        $this->amount = floatval($data["amount"]);
    }

    public function getTaxId(): int
    {
        return $this->taxId;
    }

    public function getTaxName(): string
    {
        return $this->taxName;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}
