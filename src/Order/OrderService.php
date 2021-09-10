<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Order;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\StreamInterface;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Exception\AccessDenied;
use Wizaplace\SDK\Exception\Conflict;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\OrderNotCancellable;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;
use Wizaplace\SDK\Exception\UnauthorizedModerationAction;
use Wizaplace\SDK\PaginatedData;
use Wizaplace\SDK\Subscription\SubscriptionSummary;
use Wizaplace\SDK\Vendor\Order\Shipment;

/**
 * Class OrderService
 * @package Wizaplace\SDK\Order
 *
 * This service helps retrieve and manage orders that already exist.
 *
 * If you want to *create* an order, you need to use the BasketService.
 */
class OrderService extends AbstractService
{
    /**
     * List the orders of the current user.
     *
     * @deprecated Using getOrders() is deprecated, use getPaginatedOrders() instead.
     *
     * @param array $sort
     * @param int  $start
     * @param int  $limit
     *
     * @return Order[]
     *
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getOrders(array $sort = null, int $start = null, int $limit = null): array
    {
        $this->client->mustBeAuthenticated();

        $datas = $this->client->get(
            'user/orders',
            [
                RequestOptions::QUERY => $this->prepareQueryParams($sort, $start, $limit),
            ]
        );

        $orders = array_map(
            static function (array $orderData): Order {
                return new Order($orderData);
            },
            $datas
        );

        return $orders;
    }

    protected function prepareQueryParams(array $sort = null, int $start = null, int $limit = null): array
    {
        $params = [];

        if (\is_array($sort)) {
            $stringSort = '';
            foreach ($sort as $key => $value) {
                $stringSort .= $key . ':' . $value . ',';
            }

            $params['sort'] = $stringSort ;
        }

        if (\is_int($start) === true) {
            $params['start'] = $start ;
        }

        if (\is_int($limit) === true) {
            $params['limit'] = $limit ;
        }

        return $params;
    }

    /**
     * List the paginated orders of the current user.
     *
     * @param array $sort
     * @param int  $start
     * @param int  $limit
     *
     * @return PaginatedData
     *
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getPaginatedOrders(array $sort = null, int $start = null, int $limit = null): PaginatedData
    {
        $this->client->mustBeAuthenticated();

        [$data, $paginationHeaders] = $this->client->getWithPaginationHeaders(
            'user/orders',
            [
                RequestOptions::QUERY => $this->prepareQueryParams($sort, $start, $limit),
            ]
        );

        $orders = array_map(
            static function (array $orderData): Order {
                return new Order($orderData);
            },
            $data
        );

        return new PaginatedData(
            (int) $paginationHeaders->getLimit(),
            (int) $paginationHeaders->getOffset(),
            (int) $paginationHeaders->getTotal(),
            $orders
        );
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

        return new Order($this->client->get('user/orders/' . $orderId, []));
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
        $orderReturns = array_map(
            static function (array $orderReturn): OrderReturn {
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
            $this->client->post(
                "user/orders/{$request->getOrderId()}/after-sales",
                [
                    RequestOptions::JSON => [
                        'comments' => $request->getComments(),
                        'items' => $request->getItemsDeclinationsIds(),
                    ],
                ]
            );
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

        $this->client->post(
            "orders/$orderId/adjustments",
            [
                'json' => [
                    'itemId' => $itemId,
                    'newTotalWithoutTaxes' => $newPrice,
                ],
            ]
        );

        return $this;
    }

    public function getAdjustments(int $orderId): array
    {
        $this->client->mustBeAuthenticated();
        try {
            return array_map(
                function (array $data): OrderAdjustment {
                    return new OrderAdjustment($data);
                },
                $this->client->get("orders/{$orderId}/adjustments")
            );
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("Order #{$orderId} not found", $e);
            }
            throw $e;
        }
    }

    /** @return SubscriptionSummary[] */
    public function getSubscriptions(int $orderId): array
    {
        $this->client->mustBeAuthenticated();

        return array_map(
            function (array $data): SubscriptionSummary {
                return new SubscriptionSummary($data);
            },
            $this->client->get("user/orders/${orderId}/subscriptions")
        );
    }

    public function cancelOrder(int $orderId, string $message = null): void
    {
        $this->client->mustBeAuthenticated();

        try {
            $this->client->post(
                'orders/' . $orderId . '/cancel',
                [
                    'json' => [
                        'message' => $message,
                    ],
                ]
            );
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("Order #{$orderId} not found", $e);
            }

            if ($e->getResponse()->getStatusCode() === 400) {
                throw new OrderNotCancellable('This order is not cancellable', [], $e);
            }

            throw $e;
        }
    }

    /** @return Refund[] */
    public function getOrderRefunds(int $orderId): array
    {
        $this->client->mustBeAuthenticated();

        try {
            return array_map(
                function (array $data): Refund {
                    return new Refund($data);
                },
                $this->client->get("user/orders/{$orderId}/refunds")
            );
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("Order #{$orderId} not found", $e);
            }

            throw $e;
        }
    }

    public function getOrderRefund(int $orderId, int $refundId): Refund
    {
        $this->client->mustBeAuthenticated();
        try {
            return new Refund($this->client->get("user/orders/{$orderId}/refunds/{$refundId}"));
        } catch (ClientException $exception) {
            if ($exception->getResponse()->getStatusCode() === 404) {
                throw new NotFound("Refund #{$refundId} not found for #{$orderId}", $exception);
            }

            throw $exception;
        }
    }

    public function postRefundOrder(int $orderId, RefundRequest $request): Refund
    {
        $this->client->mustBeAuthenticated();

        try {
            $response = $this->client->post(
                "orders/{$orderId}/refunds",
                [RequestOptions::JSON => $request->toArray()]
            );

            return new Refund($response);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("Order #{$orderId} not found", $e);
            }

            throw $e;
        }
    }

    /** @return CreditNote[] */
    public function getOrderCreditNotes(int $orderId): array
    {
        $this->client->mustBeAuthenticated();

        try {
            return array_map(
                function (array $data): CreditNote {
                    return new CreditNote($data);
                },
                $this->client->get("user/orders/{$orderId}/credit-notes")
            );
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("Order #{$orderId} not found", $e);
            }
            throw $e;
        }
    }

    public function getOrderCreditNote(int $orderId, int $refundId): StreamInterface
    {
        $this->client->mustBeAuthenticated();

        try {
            $options = [
                RequestOptions::HEADERS => [
                    "Accept" => "application/pdf",
                ],
            ];
            $response = $this->client->rawRequest("GET", "user/orders/{$orderId}/credit-notes/{$refundId}", $options);

            return $response->getBody();
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("Order #{$orderId} not found", $e);
            }
            throw $e;
        }
    }

    /** @return Shipment[] */
    public function getOrderShipments(int $orderId): array
    {
        $this->client->mustBeAuthenticated();

        try {
            $response = $this->client->get("user/orders/{$orderId}/shipments");
            $shipments = [];

            foreach ($response as $shipment) {
                $shipments[] = new Shipment($shipment);
            };

            return $shipments;
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("Order #{$orderId} not found", $e);
            }
            throw $e;
        }
    }

    public function dispatchFunds(int $orderId): array
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->client->post('orders/' . $orderId . '/dispatch');
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case 400:
                    throw new SomeParametersAreInvalid("The Dispatch could not be processed. Check out the response payload to identify the issue.", 400, $e);
                case 401:
                    throw new UnauthorizedModerationAction("Unauthorized access.");
                case 403:
                    throw new AccessDenied("Insufficient permission.");
                case 404:
                    throw new NotFound("Order #{$orderId} not found", $e);
                default:
                    throw $e;
            }
        }
    }

    public function addExtra(int $orderId, array $extra): void
    {
        $this->client->mustBeAuthenticated();
        try {
            $this->client->post(
                sprintf('user/orders/%d/extra', $orderId),
                [
                    RequestOptions::JSON => [
                        'extra' => $extra,
                    ],
                ]
            );
        } catch (ClientException $exception) {
            switch ($exception->getResponse()->getStatusCode()) {
                case 400:
                    throw new SomeParametersAreInvalid($exception->getMessage());
                case 403:
                    throw new AccessDenied($exception->getMessage());
                case 404:
                    throw new NotFound($exception->getMessage());
                case 409:
                    throw new Conflict($exception->getMessage());
                default:
                    throw $exception;
            }
        }
    }
}
