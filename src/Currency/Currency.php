<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Currency;

/**
 * Class Currency
 * @package Wizaplace\SDK\Currency
 */
final class Currency
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var ?float
     */
    private $exchangeRate;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var ?string
     */
    private $symbol;

    /**
     * @var CurrencyCountries[]
     */
    private $countryCodes;


    public function __construct(array $data)
    {
        $this->setCode($data['code']);
        $this->setEnabled($data['enabled']);
        $this->setExchangeRate($data['exchangeRate'] ?? null);
        $this->setSymbol($data['symbol'] ?? null);
        $this->countryCodes = array_map(function (array $itemData): CurrencyCountries {
            return new CurrencyCountries($itemData);
        }, $data['countries']);
    }

    public function setExchangeRate(?float $exchangeRate): self
    {
        $this->exchangeRate = $exchangeRate;

        return $this;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getExchangeRate(): ?float
    {
        return $this->exchangeRate;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function getCountryCodes(): array
    {
        return $this->countryCodes;
    }

    protected function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    protected function setSymbol(?string $symbol): self
    {
        $this->symbol = $symbol;

        return $this;
    }
}
