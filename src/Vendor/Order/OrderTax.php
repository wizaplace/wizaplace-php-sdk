<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Order;

use function theodorejb\polycast\to_float;

final class OrderTax
{
    /** @var float */
    private $rateValue;

    /** @var bool */
    private $priceIncludesTax;

    /** @var float */
    private $taxSubtotal;

    /** @var string */
    private $description;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->rateValue = to_float($data['rate_value']);
        $this->description = $data['description'];
        $this->taxSubtotal = $data['tax_subtotal'];
        $this->priceIncludesTax = $data['price_includes_tax'] === 'Y';
    }

    public function getRateValue(): float
    {
        return $this->rateValue;
    }

    public function doesPriceIncludesTax(): bool
    {
        return $this->priceIncludesTax;
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
