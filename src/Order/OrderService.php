<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
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
final class OrderService extends AbstractService
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
        $orders = array_map(static function (array $orderData) : Order {
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
        $data = $this->client->get("user/orders/returns", []);
        $orderReturns = array_map(static function (array $orderReturn) : OrderReturn {
            return new OrderReturn($orderReturn);
        }, $data);

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
     * Return items from an order. Here is an example:
     *
     * $order = $orderService->getOrder($orderId);
     *
     * $createOrderReturn = new CreateOrderReturn($orderId, 'Comment from the user');
     *
     * $returnReasons = $orderService->getReturnReasons();
     *
     * // Here we are returning all items from the order, but the user may select only some of them.
     * foreach ($order->getOrderItem() as $orderItem) {
     *     $selectedReturnReason = reset($returnReasons); // Let the user select the reason why he is returning the item
     *
     *     $createOrderReturn->addItem($orderItem->getDeclinationId(), $selectedReturnReason->getId(), $orderItem->getAmount());
     * }
     *
     * $returnId = $orderService->createOrderReturn($createOrderReturn);
     *
     * @return int ID of the created return.
     *
     * @throws AuthenticationRequired
     */
    public function createOrderReturn(CreateOrderReturn $creationCommand): int
    {
        $this->client->mustBeAuthenticated();

        return $this->client->post(
            "user/orders/{$creationCommand->getOrderId()}/returns",
            [
                "form_params" => [
                    'userId' => $this->client->getApiKey()->getId(),
                    'comments' => $creationCommand->getComments(),
                    'items' => $creationCommand->getItems(),
                ],
            ]
        )['returnId'];
    }
}
