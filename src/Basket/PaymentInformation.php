<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Basket;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

/**
 * Class PaymentInformation
 * @package Wizaplace\SDK\Basket
 */
final class PaymentInformation
{
    /** @var BasketOrder[] */
    private $orders;
    /** @var null|UriInterface */
    private $redirectUrl;
    /** @var string */
    private $html;
    /** @var int */
    private $parentOrderId;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->orders = array_map(
            static function (array $orderData): BasketOrder {
                return new BasketOrder($orderData);
            },
            $data['orders']
        );
        $this->parentOrderId = $data['parentOrderId'];
        $this->redirectUrl = !isset($data['redirectUrl']) || $data['redirectUrl'] === '' ? null : new Uri($data['redirectUrl']);
        $this->html = $data['html'] ?? '';
    }

    /**
     * @return BasketOrder[]
     */
    public function getOrders(): array
    {
        return $this->orders;
    }

    /**
     * @return int
     */
    public function getParentOrderId(): int
    {
        return $this->parentOrderId;
    }

    /**
     * @return UriInterface|null
     */
    public function getRedirectUrl(): ?UriInterface
    {
        return $this->redirectUrl;
    }

    /**
     * @return string
     */
    public function getHtml(): string
    {
        return $this->html;
    }
}
