<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Shipping;

/**
 * Class MondialRelayBrandCode
 * @package Wizaplace\SDK\Shipping
 */
class MondialRelayBrandCode
{
    /**
     * @var string
     */
    private $value;

    /**
     * MondialRelayBrandCode constructor.
     *
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }
}
