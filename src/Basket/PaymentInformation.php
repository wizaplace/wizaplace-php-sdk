<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Basket;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

final class PaymentInformation
{
    /** @var BasketOrder[] */
    private $orders;
    /** @var null|UriInterface */
    private $redirectUrl;
    /** @var string */
    private $html;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->orders = array_map(static function (array $orderData): BasketOrder {
            return new BasketOrder($orderData);
        }, $data['orders']);
        $this->redirectUrl = !isset($data['redirectUrl']) || $data['redirectUrl'] === '' ? null : new Uri($this->redirectUrl);
        $this->html = $data['html'] ?? '';
    }

    /**
     * @return BasketOrder[]
     */
    public function getOrders(): array
    {
        return $this->orders;
    }

    public function getRedirectUrl(): ?UriInterface
    {
        return $this->redirectUrl;
    }

    public function getHtml(): string
    {
        return $this->html;
    }
}
