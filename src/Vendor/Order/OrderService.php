<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Order;

use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Shipping\MondialRelayLabel;

class OrderService extends AbstractService
{
    public function acceptOrder(int $orderId): void
    {
        $this->setOrderIsAccepted($orderId, true);
    }

    public function declineOrder(int $orderId): void
    {
        $this->setOrderIsAccepted($orderId, false);
    }

    /**
     * @param null|OrderStatus $statusFilter
     * @return OrderSummary[]
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     */
    public function listOrders(?OrderStatus $statusFilter = null): array
    {
        $this->client->mustBeAuthenticated();

        $query = [];
        if ($statusFilter !== null) {
            $query['status'] = $statusFilter->getValue();
        }
        $data = $this->client->get('orders', [
            RequestOptions::QUERY => $query,
        ]);

        return array_map(function (array $orderData): OrderSummary {
            return new OrderSummary($orderData);
        }, $data);
    }

    public function getOrderById(int $orderId): Order
    {
        $this->client->mustBeAuthenticated();
        $data = $this->client->get("orders/${orderId}");

        return new Order($data);
    }

    /**
     * @return Shipment[]
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
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

    public function getShipmentById(int $shipmentId): Shipment
    {
        $data = $this->client->get("shipments/${shipmentId}");

        return new Shipment($data);
    }

    public function createShipment(CreateShipmentCommand $command): int
    {
        $this->client->mustBeAuthenticated();
        $command->validate();

        $data = $this->client->post('shipments', [
            RequestOptions::JSON => $command->toArray(),
        ]);

        return $data['shipment_id'];
    }

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
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     */
    public function listTaxes(): array
    {
        $this->client->mustBeAuthenticated();
        $taxesData = $this->client->get('taxes');

        return array_map(static function (array $taxData): Tax {
            return new Tax($taxData);
        }, $taxesData);
    }

    public function reportHandDelivery(int $orderId, ?string $deliveryCode): void
    {
        $this->client->mustBeAuthenticated();

        $this->client->post("orders/${orderId}/handDelivery", [
            RequestOptions::JSON => [
                'code' => $deliveryCode,
            ],
        ]);
    }

    public function generateMondialRelayLabel(int $orderId, CreateLabelCommand $command)
    {
        $command->validate();

        $result = $this->client->post("_orders/${orderId}/mondialRelayLabel", [
            RequestOptions::JSON => $command->toArray(),
        ]);

        return new MondialRelayLabel($result);
    }

    private function setOrderIsAccepted(int $orderId, bool $accepted): void
    {
        $this->client->mustBeAuthenticated();
        $this->client->put("orders/${orderId}", [
            RequestOptions::JSON => [
                'approved' => $accepted,
            ],
        ]);
    }
}
