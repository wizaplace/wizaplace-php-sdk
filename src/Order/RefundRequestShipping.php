<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Order;

use Wizaplace\SDK\ArrayableInterface;

final class RefundRequestShipping implements ArrayableInterface
{
    /** @var bool */
    private $hasShipping;

    public function __construct(bool $hasShipping)
    {
        $this->hasShipping = $hasShipping;
    }

    public function hasShipping(): bool
    {
        return $this->hasShipping;
    }

    public function setHasShipping(bool $hasShipping): self
    {
        $this->hasShipping = $hasShipping;

        return $this;
    }

    /** @return mixed[] */
    public function toArray(): array
    {
        return ['hasShipping' => $this->hasShipping];
    }
}
