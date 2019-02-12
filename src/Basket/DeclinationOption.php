<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Basket;

use function theodorejb\polycast\to_string;

/**
 * Class DeclinationOption
 * @package Wizaplace\SDK\Basket
 */
final class DeclinationOption
{
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
