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

/**
 * This service helps retrieve and manage orders that already exist.
 *
 * If you want to *create* an order, you need to use the BasketService.
 */
class OrderService extends AbstractService
{
    /**
     * List the orders of the current user.
     *
     * @return Order[]
     *
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
     * Returns the order matching the given ID.
     *
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

        return new OrderReturn($this->client->get("user/orders/returns/{$returnId}", []));
    }

    /**
     * Lists all the order returns that were created by the current user.
     *
     * @return OrderReturn[]
     *
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
     * Returns all the return reasons that can be used to return an order.
     *
     * @return ReturnReason[]
     */
    public function getReturnReasons(): array
    {
        return array_map(
            ['Wizaplace\Order\ReturnReason', 'fromApiData'],
            $this->client->get('orders/returns/reasons')
        );
    }

    /**
     * Return an order.
     *
     * This method expects the list of items to return in the given order. A buyer can return
     * the whole order or only specific items.
     *
     * @param int $orderId ID of the order to return.
     * @param string $comments Buyer's comments on why they are returning the order.
     * @param array $items List of items of the order to return.
     *
     * @return int ID of the created return.
     *
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
