<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Basket;

final class PaymentInformation
{
    /** @var array */
    private $orders;
    /** @var string */
    private $redirectUrl;
    /** @var string */
    private $html;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->orders = $data['orders'];
        $this->redirectUrl = $data['redirectUrl'] ?? '';
        $this->html = $data['html'] ?? '';
    }

    public function getOrders(): array
    {
        return $this->orders;
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    public function getHtml(): string
    {
        return $this->html;
    }
}
