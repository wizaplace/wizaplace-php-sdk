<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Basket;

use function theodorejb\polycast\to_float;

/**
 * Class Price
 * @package Wizaplace\SDK\Basket
 */
final class Price
{
    /** @var float */
    private $priceWithoutVat;

    /** @var float */
    private $priceWithTaxes;

    /** @var float */
    private $vat;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->priceWithoutVat = to_float($data['priceWithoutVat']);
        $this->priceWithTaxes = to_float($data['priceWithTaxes']);
        $this->vat = to_float($data['vat']);
    }

    /**
     * @return float
     */
    public function getPriceWithoutVat(): float
    {
        return $this->priceWithoutVat;
    }

    /**
     * @return float
     */
    public function getPriceWithTaxes(): float
    {
        return $this->priceWithTaxes;
    }

    /**
     * @return float
     */
    public function getVat(): float
    {
        return $this->vat;
    }
}
