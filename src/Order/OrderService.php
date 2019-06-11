<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Order;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\StreamInterface;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

/**
 * Class OrderService
 * @package Wizaplace\SDK\Order
 *
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
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
     * @param int $orderId
     *
     * @return Order
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getOrder(int $orderId): Order
    {
        $this->client->mustBeAuthenticated();

        return new Order($this->client->get('user/orders/'.$orderId, []));
    }

    /**
     * @param int $returnId
     *
     * @return OrderReturn
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
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
     * @param CreateOrderReturn $creationCommand
     *
     * @return int ID of the created return.
     *
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
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
     *
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
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

    /**
     * @param int $orderId
     *
     * @return StreamInterface
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function downloadPdfInvoice(int $orderId): StreamInterface
    {
        $this->client->mustBeAuthenticated();
        $options = [
            RequestOptions::HEADERS => [
                "Accept" => "application/pdf",
            ],
        ];
        $response = $this->client->rawRequest("GET", "user/orders/{$orderId}/pdf-invoice", $options);

        return $response->getBody();
    }

    /**
     * @param OrderCommitmentCommand $command
     *
     * @throws NotFound
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function commitOrder(OrderCommitmentCommand $command): void
    {
        try {
            $this->client->post(
                "user/orders/{$command->getOrderId()}/commitment",
                [
                    RequestOptions::JSON => [
                        'date' => $command->getCommitDate()->format('Y-m-d'),
                        'number' => $command->getCommitNumber(),
                    ],
                ]
            );
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("Order #{$command->getOrderId()} not found", $e);
            }

            if ($e->getResponse()->getStatusCode() === 400) {
                throw new SomeParametersAreInvalid("Some parameters are invalid", 400, $e);
            }

            throw $e;
        }
    }

    public function getPayment(int $orderId): Payment
    {
        $this->client->mustBeAuthenticated();

        try {
            return new Payment($this->client->get("orders/{$orderId}/payment"));
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("Order #{$orderId} not found", $e);
            }

            throw $e;
        }
    }

    public function createOrderAdjustment(int $orderId, int $itemId, float $newPrice): self
    {
        $this->client->mustBeAuthenticated();

        $this->client->post("orders/$orderId/adjustments", [
            'json' => [
                'itemId' => $itemId,
                'newPrice' => $newPrice,
            ],
        ]);

        return $this;
    }
}
