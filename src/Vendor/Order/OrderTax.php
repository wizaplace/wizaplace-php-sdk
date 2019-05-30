<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Order;

use function theodorejb\polycast\to_float;

/**
 * Class OrderTax
 * @package Wizaplace\SDK\Vendor\Order
 */
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
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->rateValue = to_float($data['rate_value']);
        $this->description = $data['description'];
        $this->taxSubtotal = $data['tax_subtotal'];
        $this->priceIncludesTax = $data['price_includes_tax'] === 'Y';
    }

    /**
     * @return float
     */
    public function getRateValue(): float
    {
        return $this->rateValue;
    }

    /**
     * @return bool
     */
    public function doesPriceIncludesTax(): bool
    {
        return $this->priceIncludesTax;
    }

    /**
     * @return float
     */
    public function getTaxSubtotal(): float
    {
        return $this->taxSubtotal;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
