<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Order;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\StreamInterface;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Exception\AccessDenied;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;
use Wizaplace\SDK\Order\OrderAdjustment;
use Wizaplace\SDK\Shipping\MondialRelayLabel;
use Wizaplace\SDK\Transaction\Transaction;

/**
 * Class OrderService
 * @package Wizaplace\SDK\Vendor\Order
 */
class OrderService extends AbstractService
{
    /**
     * @param int    $orderId
     * @param bool   $createInvoice
     * @param string $invoiceNumber
     * @param bool   $createBillingNumber
     *
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function acceptOrder(int $orderId, bool $createInvoice = false, string $invoiceNumber = "", bool $createBillingNumber = false): void
    {
        if ($createInvoice && empty($invoiceNumber) && !$createBillingNumber) {
            throw new SomeParametersAreInvalid("If you choose to create an invoice, you need to set a number");
        }

        $options = [
            'approved' => true,
        ];

        if ($createBillingNumber) {
            $options['create_automatic_billing_number'] = true;
        } else {
            $options['do_not_create_invoice'] = $createInvoice;
            if ($createInvoice) {
                $options['invoice_number'] = $invoiceNumber;
            }
        }

        $this->client->mustBeAuthenticated();
        $this->client->put("orders/${orderId}", [
            RequestOptions::JSON => $options,
        ]);
    }

    /**
     * @param int    $orderId
     * @param string $declineReason
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function declineOrder(int $orderId, $declineReason = ''): void
    {
        $this->client->mustBeAuthenticated();
        $this->client->put("orders/${orderId}", [
            RequestOptions::JSON => [
                'approved' => false,
                'decline_reason' => $declineReason,
            ],
        ]);
    }

    /**
     * @param OrderStatus|null $statusFilter
     * @param OrderListFilter|null $additionalFilter
     *
     * @return OrderSummary[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function listOrders(?OrderStatus $statusFilter = null, ?OrderListFilter $additionalFilter = null): array
    {
        $this->client->mustBeAuthenticated();

        $query = [];
        if ($statusFilter instanceof OrderStatus) {
            $query['status'] = $statusFilter->getValue();
        }

        if ($additionalFilter instanceof OrderListFilter) {
            $query = array_merge($query, $additionalFilter->toArray());
        }

        $data = $this->client->get('orders', [
            RequestOptions::QUERY => $query,
        ]);

        return array_map(function (array $orderData): OrderSummary {
            return new OrderSummary($orderData);
        }, $data);
    }

    /**
     * @param int $orderId
     *
     * @return Order
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     * @throws \Exception
     */
    public function getOrderById(int $orderId): Order
    {
        $this->client->mustBeAuthenticated();
        $data = $this->client->get("orders/${orderId}");

        return new Order($data);
    }

    /**
     * @param int|null $orderIdFilter
     *
     * @return Shipment[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function listShipments(?int $orderIdFilter = null): array
    {
        $this->client->mustBeAuthenticated();

        $query = [];
        if ($orderIdFilter !== null) {
            $query['order_id'] = $orderIdFilter;
        }
        $data = $this->client->get('shipments', [
            RequestOptions::QUERY => $query,
        ]);

        return array_map(static function (array $shipmentData): Shipment {
            return new Shipment($shipmentData);
        }, $data);
    }

    /**
     * @param int $shipmentId
     *
     * @return Shipment
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getShipmentById(int $shipmentId): Shipment
    {
        $data = $this->client->get("shipments/${shipmentId}");

        return new Shipment($data);
    }

    /**
     * @param CreateShipmentCommand $command
     *
     * @return int
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function createShipment(CreateShipmentCommand $command): int
    {
        $this->client->mustBeAuthenticated();
        $command->validate();

        $data = $this->client->post('shipments', [
            RequestOptions::JSON => $command->toArray(),
        ]);

        return $data['shipment_id'];
    }

    /**
     * @param int    $orderId
     * @param string $invoiceNumber
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function setInvoiceNumber(int $orderId, string $invoiceNumber): void
    {
        $this->client->mustBeAuthenticated();
        $this->client->put("orders/${orderId}", [
            RequestOptions::JSON => [
                'invoice_number' => $invoiceNumber,
            ],
        ]);
    }

    /**
     * @return Tax[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function listTaxes(): array
    {
        $this->client->mustBeAuthenticated();
        $taxesData = $this->client->get('taxes');

        return array_map(static function (array $taxData): Tax {
            return new Tax($taxData);
        }, $taxesData);
    }

    /**
     * @param int $orderId
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getHandDeliveryCodes(int $orderId): array
    {
        $this->client->mustBeAuthenticated();

        return $this->client->get("orders/${orderId}/handDelivery");
    }

    /**
     * @param int         $orderId
     * @param string|null $deliveryCode
     *
     * @throws AccessDenied
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function reportHandDelivery(int $orderId, ?string $deliveryCode): void
    {
        $this->client->mustBeAuthenticated();

        try {
            $this->client->post("orders/${orderId}/handDelivery", [
                RequestOptions::JSON => [
                    'code' => $deliveryCode,
                ],
            ]);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 400) {
                throw new SomeParametersAreInvalid($e->getMessage(), 400, $e);
            }

            if ($e->getResponse()->getStatusCode() === 403) {
                throw new AccessDenied("You're not allowed to access to this order", 403);
            }

            throw $e;
        }
    }

    /**
     * @param int                $orderId
     * @param CreateLabelCommand $command
     *
     * @return MondialRelayLabel
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function generateMondialRelayLabel(int $orderId, CreateLabelCommand $command)
    {
        $command->validate();

        $result = $this->client->post("_orders/${orderId}/mondialRelayLabel", [
            RequestOptions::JSON => $command->toArray(),
        ]);

        return new MondialRelayLabel($result);
    }

    /**
     * @param int $orderId
     * @param string $details
     * @return OrderService
     */
    public function setOrderDetails(int $orderId, string $details): self
    {
        $this->client->mustBeAuthenticated();
        $this->client->patch("orders/$orderId/details", [
            RequestOptions::JSON => [
                "details" => $details,
            ],
        ]);

        return $this;
    }

    public function createOrderAdjustment(int $orderId, int $itemId, float $newPrice): self
    {
        $this->client->mustBeAuthenticated();

        $this->client->post("orders/$orderId/adjustments", [
            RequestOptions::JSON => [
                'itemId' => $itemId,
                'newTotalWithoutTaxes' => $newPrice,
            ],
        ]);

        return $this;
    }

    public function getAdjustments(int $orderId): array
    {
        $this->client->mustBeAuthenticated();
        try {
            return array_map(function (array $data): OrderAdjustment {
                return new OrderAdjustment($data);
            }, $this->client->get("orders/{$orderId}/adjustments"));
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("Order #{$orderId} not found", $e);
            }
            throw $e;
        }
    }

    /** @return Transaction[] */
    public function getTransactions(int $orderId): array
    {
        $this->client->mustBeAuthenticated();

        return array_map(function (array $data): Transaction {
            return new Transaction($data);
        }, $this->client->get("orders/${orderId}/transactions"));
    }

    /**
     * @param int $orderId
     *
     * @throws AuthenticationRequired
     * @throws GuzzleException
     *
     * @return StreamInterface
     */
    public function downloadPdfInvoice(int $orderId): StreamInterface
    {
        $this->client->mustBeAuthenticated();

        $options = [
            RequestOptions::HEADERS => [
                "Accept" => "application/pdf",
            ],
        ];

        $response = $this->client->rawRequest("GET", "orders/{$orderId}/pdf-invoice", $options);

        return $response->getBody();
    }
}
