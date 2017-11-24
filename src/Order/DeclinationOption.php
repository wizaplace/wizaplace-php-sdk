<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Order;

use function theodorejb\polycast\to_string;

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
     */
    public function __construct(array $data)
    {
        $this->optionId = $data['optionId'];
        $this->optionName = to_string($data['optionName']);
        $this->variantId = $data['variantId'];
        $this->variantName = to_string($data['variantName']);
    }

    public function getOptionId(): int
    {
        return $this->optionId;
    }

    public function getOptionName(): string
    {
        return $this->optionName;
    }

    public function getVariantId(): int
    {
        return $this->variantId;
    }

    public function getVariantName(): string
    {
        return $this->variantName;
    }
}
