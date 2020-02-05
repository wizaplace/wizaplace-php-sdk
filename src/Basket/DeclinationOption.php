<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Basket;

use function theodorejb\polycast\to_string;

/**
 * Class DeclinationOption
 * @package Wizaplace\SDK\Basket
 */
final class DeclinationOption
{
    /** @var string|null */
    private $optionCode;

    /** @var int */
    private $optionId;

    /** @var string */
    private $optionName;

    /** @var int */
    private $variantId;

    /** @var string */
    private $variantName;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->optionId = $data['optionId'];
        $this->optionName = to_string($data['optionName']);
        $this->optionCode = array_key_exists('optionCode', $data) && is_string($data['optionCode'])
            ? $data['optionCode'] : null;
        $this->variantId = $data['variantId'];
        $this->variantName = to_string($data['variantName']);
    }

    /**
     * @return int
     */
    public function getOptionId(): int
    {
        return $this->optionId;
    }

    /**
     * @return string
     */
    public function getOptionName(): string
    {
        return $this->optionName;
    }

    public function getOptionCode(): ?string
    {
        return $this->optionCode;
    }

    /**
     * @return int
     */
    public function getVariantId(): int
    {
        return $this->variantId;
    }

    /**
     * @return string
     */
    public function getVariantName(): string
    {
        return $this->variantName;
    }
}
