<?php
/**
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Basket;

use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

final class CheckoutWithPreauthTokenCommand extends CheckoutCommand
{
    /** @var string|null */
    private $preauthToken;

    public function getPreauthToken(): ?string
    {
        return $this->preauthToken;
    }

    public function setPreauthToken(?string $preauthToken): void
    {
        $this->preauthToken = $preauthToken;
    }

    /**
     * @internal
     * @throws SomeParametersAreInvalid
     */
    public function validate(): void
    {
        parent::validate();
        if (!isset($this->preauthToken)) {
            throw new SomeParametersAreInvalid('Missing preauthToken');
        }
    }

    public function serialize(): array
    {
        $serializedCheckout = parent::serialize();
        $serializedCheckout['preauthToken'] = $this->preauthToken;

        return $serializedCheckout;
    }
}
