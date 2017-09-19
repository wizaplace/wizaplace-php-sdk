<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Order;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

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
            ['Wizaplace\SDK\Order\ReturnReason', 'fromApiData'],
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

    /**
     * @param AfterSalesServiceRequest $request
     * @throws SomeParametersAreInvalid
     * @throws AuthenticationRequired
     */
    public function sendAfterSalesServiceRequest(AfterSalesServiceRequest $request): void
    {
        $request->validate();
        $this->client->mustBeAuthenticated();

        try {
            $this->client->post("user/orders/{$request->getOrderId()}/after-sales", [
                RequestOptions::JSON => [
                    'comments' => $request->getComments(),
                    'items' => $request->getItemsDeclinationsIds(),
                ],
            ]);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("Order #{$request->getOrderId()} not found", $e);
            }

            if ($e->getResponse()->getStatusCode() === 400) {
                throw new SomeParametersAreInvalid("Some parameters are invalid", 400, $e);
            }

            throw $e;
        }
    }
}
