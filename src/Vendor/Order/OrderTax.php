<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Order;

use function theodorejb\polycast\to_float;

class OrderTax
{
    /** @var TaxRateType */
    private $rateType;

    /** @var float */
    private $rateValue;

    /** @var bool */
    private $priceIncludesTax;

    /** @var string */
    private $regNumber;

    /** @var int */
    private $priority;

    /** @var float */
    private $taxSubtotal;

    /** @var string */
    private $description;

    /** @var array */
    private $applies;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->rateType = new TaxRateType($data['rate_type']);
        $this->rateValue = to_float($data['rate_value']);
        $this->regNumber = $data['regnumber'];
        $this->description = $data['description'];
        $this->taxSubtotal = $data['tax_subtotal'];
        $this->priority = $data['priority'];
        $this->priceIncludesTax = $data['price_includes_tax'] === 'Y';
        $this->applies = $data['applies'];
    }

    public function getRateType(): TaxRateType
    {
        return $this->rateType;
    }

    public function getRateValue(): float
    {
        return $this->rateValue;
    }

    public function doesPriceIncludesTax(): bool
    {
        return $this->priceIncludesTax;
    }

    public function getRegNumber(): string
    {
        return $this->regNumber;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getTaxSubtotal(): float
    {
        return $this->taxSubtotal;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
