<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Order;

use Wizaplace\AbstractService;
use Wizaplace\User\ApiKey;

class OrderService extends AbstractService
{
    public function getOrders(ApiKey $apiKey): array
    {
        $datas = $this->get('user/orders', [], $apiKey);
        $orders = array_map(function ($orderData) {
            return new Order($orderData);
        }, $datas);

        return $orders;
    }

    public function getOrder(int $orderId, ApiKey $apiKey): Order
    {
        return new Order($this->get('user/orders/'.$orderId, [], $apiKey));
    }

    public function getOrderReturn(int $returnId, ApiKey $apiKey): OrderReturn
    {
        return new OrderReturn($this->get("user/returns/{$returnId}", [], $apiKey));
    }

    /**
     * @return OrderReturn[]
     */
    public function getOrderReturns(ApiKey $apiKey): array
    {
        $data = $this->get("user/returns", [], $apiKey);
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
            $this->get('orders/returns/types')
        );

        return $returnTypes;
    }
    /**
     * @return int returnId
     */
    public function createOrderReturn(int $orderId, string $comments, array $items, ApiKey $apiKey): int
    {
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

        return $this->post(
            "user/orders/{$orderId}/returns",
            [
                "form_params" => [
                    'userId' => $apiKey->getId(),
                    'comments' => $comments,
                    'items' => $items,
                ],
            ],
            $apiKey
        )['returnId'];
    }
}
