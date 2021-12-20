<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK\QuoteRequest\QuoteRequestSelection;

class QuoteRequestSelectionDeclination
{
    /** @var string */
    private $declinationId;

    /** @var int */
    private $quantity;

    public function __construct(string $declinationId, int $quantity)
    {
        $this->declinationId = $declinationId;
        $this->quantity = $quantity;
    }

    public function getdeclinationId(): string
    {
        return $this->declinationId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
