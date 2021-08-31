<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Discussion;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\HttpFoundation\Response;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Catalog\DeclinationId;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\ProductNotFound;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;
use Wizaplace\SDK\Exception\UnauthorizedModerationAction;

/**
 * Class DiscussionService
 * @package Wizaplace\SDK\Discussion
 *
 * This service helps getting and creating discussions and messages.
 *
 * Discussion are a list of exchanged messages between a customer and a product's vendor.
 *
 * Example :
 *
 *      // Get the user's discussions list
 *      $discussionsList = $discussionService->getDiscussions();
 *
 *      // Get a discussion based on its id
 *      $discussion = $discussionService->getDiscussion($discussionId);
 *
 *      // Start a discussion between current User and Product's vendor
 *      $discussion = $discussionService->startDiscussion($productId);
 *
 *      // Get the discussion's messages list
 *      $messagesList = $discussionService->startDiscussion($productId);
 *
 *      // Post a new message in the discussion
 *      $message = $discussionService->postMessage($discussionId, $content);
 */
class DiscussionService extends AbstractService
{
    /**
     * Get the user's discussions list
     *
     * @return Discussion[]
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getDiscussions(): array
    {
        $this->client->mustBeAuthenticated();

        $discussions = array_map(
            static function (array $discussionData): Discussion {
                return new Discussion($discussionData);
            },
            $this->client->get('discussions')
        );

        return $discussions;
    }

    /**
     * Get a discussion based on its id
     *
     * @param int $discussionId
     *
     * @return Discussion
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getDiscussion(int $discussionId): Discussion
    {
        $this->client->mustBeAuthenticated();

        return new Discussion($this->client->get('discussions/' . $discussionId));
    }

    /**
     * Start a discussion with a vendor about a specific product.
     *
     * @param int $productId
     *
     * @return Discussion
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function startDiscussion(int $productId): Discussion
    {
        $this->client->mustBeAuthenticated();

        $discussionData = $this->client->post('discussions', [RequestOptions::JSON => ['productId' => $productId]]);

        return new Discussion($discussionData);
    }

    /**
     * Start a discussion with a vendor.
     *
     * @param int $companyId
     *
     * @return Discussion
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function startDiscussionWithVendor(int $companyId): Discussion
    {
        $this->client->mustBeAuthenticated();

        $discussionData = $this->client->post('discussions', [RequestOptions::JSON => ['companyId' => $companyId]]);

        return new Discussion($discussionData);
    }

    /**
     * Start a discussion with a vendor about a specific product identified by its declination ID.
     *
     * @param DeclinationId $declinationId
     *
     * @return Discussion
     * @throws AuthenticationRequired
     * @throws ProductNotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function startDiscussionFromDeclinationId(DeclinationId $declinationId): Discussion
    {
        $this->client->mustBeAuthenticated();

        try {
            $discussionData = $this->client->post('discussions', [RequestOptions::JSON => ['declinationId' => (string) $declinationId]]);

            return new Discussion($discussionData);
        } catch (ProductNotFound $e) {
            // add declinationId to exception's context
            throw new ProductNotFound($e->getMessage(), array_merge($e->getContext(), ['declinationId' => (string) $declinationId]), $e);
        }
    }

    /**
     * Get the discussion's messages list
     *
     * @param int $discussionId
     *
     * @return Message[]
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getMessages(int $discussionId): array
    {
        $this->client->mustBeAuthenticated();

        $messages = $this->client->get('discussions/' . $discussionId . '/messages');
        $userId = $this->client->getApiKey()->getId();

        return array_map(
            static function (array $messageData) use ($userId): Message {
                $messageData['isAuthor'] = ($messageData['authorId'] === $userId);

                return new Message($messageData);
            },
            $messages
        );
    }

    /**
     * Post a new message in the discussion
     *
     * @param int    $discussionId
     * @param string $content
     * @param array|null $files
     *
     * @return Message
     * @throws AuthenticationRequired
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function postMessage(int $discussionId, string $content, array $files = null): Message
    {
        $this->client->mustBeAuthenticated();

        if (\is_array($files) === true && \count($files) > 0) {
            /** @var UploadedFileInterface $file */
            foreach ($files as $file) {
                if (false === $file instanceof UploadedFileInterface) {
                    throw new \InvalidArgumentException('The $files parameter must be an array of ' . UploadedFileInterface::class . '.');
                }

                $data[] = [
                    'name'     => 'attachments[]',
                    'contents' => $file->getStream(),
                    'filename' => $file->getClientFilename(),
                ];
            }
        }

        $data[] = [
            'name' => 'content',
            'contents' => $content,
        ];

        try {
            $messageData = $this->client->post('discussions/' . $discussionId . '/messages', [RequestOptions::MULTIPART => $data]);

            $messageData['isAuthor'] = ($messageData['authorId'] === $this->client->getApiKey()->getId());

            return new Message($messageData);
        } catch (ClientException $e) {
            throw new SomeParametersAreInvalid("Some parameters are invalid", 400, $e);
        }
    }

    /**
     * @param string                  $senderEmail
     * @param string                  $subject
     * @param string                  $message
     * @param null|string             $recipientEmail
     * @param string[]                $attachmentsUrls
     * @param UploadedFileInterface[] $files
     *
     * @return DiscussionService
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function submitContactRequest(
        string $senderEmail,
        string $subject,
        string $message,
        string $recipientEmail = null,
        array $attachmentsUrls = [],
        array $files = []
    ): self {
        $data = [
            [
                'name' => 'senderEmail',
                'contents' => $senderEmail,
            ],
            [
                'name' => 'subject',
                'contents' => $subject,
            ],
            [
                'name' => 'message',
                'contents' => $message,
            ],
        ];

        if (\is_string($recipientEmail)) {
            $data[] = [
                'name' => 'recipientEmail',
                'contents' => $recipientEmail,
            ];
        }

        if (\count($attachmentsUrls) > 0) {
            foreach ($attachmentsUrls as $url) {
                $data[] = [
                    'name'     => 'attachments[]',
                    'contents' => $url,
                ];
            }
        }

        if (\count($files) > 0) {
            /** @var UploadedFileInterface $file */
            foreach ($files as $file) {
                if (false === $file instanceof UploadedFileInterface) {
                    throw new \InvalidArgumentException('The $files parameter must be an array of UploadedFileInterface');
                }

                $data[] = [
                    'name'     => 'attachments[]',
                    'contents' => $file->getStream(),
                    'filename' => $file->getClientFilename(),
                ];
            }
        }

        $this->client->post(
            'contact-request',
            [
                RequestOptions::MULTIPART => $data,
            ]
        );

        return $this;
    }


    /**
     * Vendor start a discussion with a Customer.
     *
     * @param int $userId
     *
     * @return Discussion
     *
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws SomeParametersAreInvalid
     * @throws UnauthorizedModerationAction
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function startDiscussionWithCustomer(int $userId): Discussion
    {
        $this->client->mustBeAuthenticated();

        try {
            $discussionData = $this->client->post('discussions',
                [
                RequestOptions::JSON =>
                    [
                        'userId' => $userId
                    ]
                ]
            );

            return new Discussion($discussionData);
        } catch (ClientException $e) {
            $this->clientException($e);
        }
    }

    /**
     * @param ClientException $e
     *
     * @throws NotFound
     * @throws SomeParametersAreInvalid
     * @throws UnauthorizedModerationAction
     */
    private function clientException(ClientException $e): void
    {
        switch ($e->getResponse()->getStatusCode()) {
            case Response::HTTP_BAD_REQUEST:
                throw new SomeParametersAreInvalid($e->getMessage());
            case Response::HTTP_NOT_FOUND:
                throw new NotFound($e->getMessage());
            case Response::HTTP_UNAUTHORIZED:
                throw new UnauthorizedModerationAction($e->getMessage());

            default:
                throw $e;
        }
    }
}
