<?php
/**
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Basket;

use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

final class CheckoutWithRedirectUrlCommand extends CheckoutCommand
{
    /** @var string|null */
    private $redirectUrl;

    /** @var string|null */
    private $cssUrl;

    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    public function setRedirectUrl(?string $redirectUrl): void
    {
        $this->redirectUrl = $redirectUrl;
    }

    public function getCssUrl(): ?string
    {
        return $this->cssUrl;
    }

    public function setCssUrl(?string $cssUrl): void
    {
        $this->cssUrl = $cssUrl;
    }

    public function validate(): void
    {
        parent::validate();
        if (!isset($this->redirectUrl)) {
            throw new SomeParametersAreInvalid('Missing redirect Url');
        }
    }

    public function serialize(): array
    {
        $serializedCheckout = parent::serialize();
        $serializedCheckout['redirectUrl'] = $this->redirectUrl;
        if ($this->cssUrl) {
            $serializedCheckout['css'] = $this->cssUrl;
        }

        return $serializedCheckout;
    }
}
