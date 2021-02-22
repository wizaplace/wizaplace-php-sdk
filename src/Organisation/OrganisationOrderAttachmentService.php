<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Organisation;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\Response;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Order\AttachmentsOrder;
use Wizaplace\SDK\Exception\NotFound;

class OrganisationOrderAttachmentService extends AbstractService
{
    public function getOrganisationOrderAttachment(int $orderId, string $attachmentId): AttachmentsOrder
    {
        $this->client->mustBeAuthenticated();

        try {
            $response = $this->client->get("organisations/orders/{$orderId}/attachments/{$attachmentId}");

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

    public function downloadOrganisationOrderAttachment(int $orderId, string $attachmentId): StreamInterface
    {
        $this->client->mustBeAuthenticated();

        try {
            $response = $this->client->rawRequest("GET", "organisations/orders/{$orderId}/attachments/{$attachmentId}/download");

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
