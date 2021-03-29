<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Order;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\Response;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Exception\NotFound;

class OrderAttachmentService extends AbstractService
{
    public function getOrderAttachment(int $orderId, string $attachmentId): AttachmentsOrder
    {
        $this->client->mustBeAuthenticated();

        try {
            $response = $this->client->get("user/orders/{$orderId}/attachments/{$attachmentId}");

            return new AttachmentsOrder($response);
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case Response::HTTP_FORBIDDEN:
                    throw new \Exception($e->getMessage(), Response::HTTP_FORBIDDEN, $e);

                case Response::HTTP_NOT_FOUND:
                    throw new NotFound($e->getMessage());

                default:
                    throw $e;
            }
        }
    }

    public function downloadOrderAttachment(int $orderId, string $attachmentId): StreamInterface
    {
        $this->client->mustBeAuthenticated();

        try {
            $response = $this->client->rawRequest("GET", "user/orders/{$orderId}/attachments/{$attachmentId}/download");

            return $response->getBody();
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case Response::HTTP_FORBIDDEN:
                    throw new \Exception($e->getMessage(), Response::HTTP_FORBIDDEN, $e);

                case Response::HTTP_NOT_FOUND:
                    throw new NotFound($e->getMessage());

                default:
                    throw $e;
            }
        }
    }
}
