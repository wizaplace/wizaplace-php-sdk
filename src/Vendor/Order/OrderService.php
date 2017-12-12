<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Order;

use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;

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

    public function getOrderById(int $orderId): Order
    {
        $this->client->mustBeAuthenticated();
        $data = $this->client->get("orders/${orderId}");

        return new Order($data);
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
