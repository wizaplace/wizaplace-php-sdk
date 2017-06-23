<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Order;

use Wizaplace\AbstractService;
use Wizaplace\Authentication\AuthenticationRequired;

class OrderService extends AbstractService
{
    /**
     * @throws AuthenticationRequired
     */
    public function getOrders(): array
    {
        $this->client->mustBeAuthenticated();
        $datas = $this->client->get('user/orders', []);
        $orders = array_map(function ($orderData) {
            return new Order($orderData);
        }, $datas);

        return $orders;
    }

    /**
     * @throws AuthenticationRequired
     */
    public function getOrder(int $orderId): Order
    {
        $this->client->mustBeAuthenticated();

        return new Order($this->client->get('user/orders/'.$orderId, []));
    }

    /**
     * @throws AuthenticationRequired
     */
    public function getOrderReturn(int $returnId): OrderReturn
    {
        $this->client->mustBeAuthenticated();

        return new OrderReturn($this->client->get("user/returns/{$returnId}", []));
    }

    /**
     * @return OrderReturn[]
     * @throws AuthenticationRequired
     */
    public function getOrderReturns(): array
    {
        $this->client->mustBeAuthenticated();
        $data = $this->client->get("user/returns", []);
        $orderReturns = array_map(
            function ($orderReturn) {
                return new OrderReturn($orderReturn);
            },
            $data
        );

        return $orderReturns;
    }

    /**
     * @return ReturnType[]
     */
    public function getReturnTypes(): array
    {
        $returnTypes = array_map(
            ['Wizaplace\Order\ReturnType', 'fromApiData'],
            $this->client->get('orders/returns/types')
        );

        return $returnTypes;
    }

    /**
     * @return int returnId
     * @throws AuthenticationRequired
     */
    public function createOrderReturn(int $orderId, string $comments, array $items): int
    {
        $this->client->mustBeAuthenticated();
        $items = array_map(
            function (ReturnItem $item) {
                return [
                    'declinationId' => $item->getDeclinationId(),
                    'reason' => $item->getReason(),
                    'amount' => $item->getAmount(),
                ];
            },
            $items
        );

        return $this->client->post(
            "user/orders/{$orderId}/returns",
            [
                "form_params" => [
                    'userId' => $this->client->getApiKey()->getId(),
                    'comments' => $comments,
                    'items' => $items,
                ],
            ]
        )['returnId'];
    }
}
