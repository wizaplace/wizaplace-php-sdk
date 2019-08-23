<?php
/**
 *  @author      Wizacha DevTeam <dev@wizacha.com>
 *  @copyright   Copyright (c) Wizacha
 *  @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

class ExtendedPriceTier extends PriceTier
{


    /** @var null|float */
    protected $taxes;

    /** @var null|float */
    protected $priceIncludeTax;

    /** @var null|float */
    protected $priceExcludingTaxes;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->setTaxes($data['taxes']);
        $this->setPriceIncludeTax($data['includingTaxes']);
        $this->setPriceExcludingTaxes($data['excludingTaxes']);
    }

    /**
     * @return float|null
     */
    public function getTaxes(): ?float
    {
        return $this->taxes;
    }

    /**
     * @param float|null $taxes
     * @return ExtendedPriceTier
     */
    public function setTaxes(?float $taxes): self
    {
        $this->taxes = $taxes;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getPriceIncludeTax(): ?float
    {
        return $this->priceIncludeTax;
    }

    /**
     * @param float|null $priceIncludeTax
     * @return ExtendedPriceTier
     */
    public function setPriceIncludeTax(?float $priceIncludeTax): self
    {
        $this->priceIncludeTax = $priceIncludeTax;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getPriceExcludingTaxes(): ?float
    {
        return $this->priceExcludingTaxes;
    }

    /**
     * @param float|null $priceExcludingTaxes
     * @return ExtendedPriceTier
     */
    public function setPriceExcludingTaxes(?float $priceExcludingTaxes): self
    {
        $this->priceExcludingTaxes = $priceExcludingTaxes;

        return  $this;
    }
}
