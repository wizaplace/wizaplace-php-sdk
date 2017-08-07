<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Catalog;

class Combination
{
    /** @var integer */
    private $optionId;

    /** @var string */
    private $optionName;

    /** @var integer */
    private $variantId;

    /** @var string */
    private $variantName;

    public function __construct(array $data)
    {
        $this->optionId = $data['optionId'];
        $this->optionName = $data['optionName'];
        $this->variantId = $data['variantId'];
        $this->variantName = $data['variantName'];
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
