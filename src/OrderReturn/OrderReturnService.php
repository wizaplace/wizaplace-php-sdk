<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\OrderReturn;

use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Authentication\AuthenticationRequired;

/**
 * This service helps retrieve and manage returns.
 */
final class OrderReturnService extends AbstractService
{
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
        return array_map(function (array $reasonApiData) {
            return ReturnReason::fromApiData($reasonApiData);
        }, $this->client->get('orders/returns/reasons'));
    }

    /**
     * Return items from an order. Here is an example:
     *
     * $order = $orderService->getOrder($orderId);
     *
     * $createOrderReturn = new CreateOrderReturn($orderId, 'Comment from the user');
     *
     * $returnReasons = $orderReturnService->getReturnReasons();
     *
     * // Here we are returning all items from the order, but the user may select only some of them.
     * foreach ($order->getOrderItem() as $orderItem) {
     *     $selectedReturnReason = reset($returnReasons); // Let the user select the reason why he is returning the item
     *
     *     $createOrderReturn->addItem($orderItem->getDeclinationId(), $selectedReturnReason->getId(), $orderItem->getAmount());
     * }
     *
     * $returnId = $orderReturnService->createOrderReturn($createOrderReturn);
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
