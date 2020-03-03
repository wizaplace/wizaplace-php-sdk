<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Order;

use Wizaplace\SDK\Pim\Option\SystemOption;

use function theodorejb\polycast\to_string;

/**
 * Class DeclinationOption
 * @package Wizaplace\SDK\Order
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

    /** @var null|SystemOption */
    private $code = null;

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
        if (\array_key_exists('optionCode', $data)) {
            $this->code = new SystemOption($data['code']);
        }
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

    /** @return null|SystemOption */
    public function getCode(): ?SystemOption
    {
        return $this->code;
    }
}
