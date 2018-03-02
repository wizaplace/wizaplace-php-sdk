<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Discussion;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Catalog\DeclinationId;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\ProductNotFound;
use Wizaplace\SDK\Exception\SenderIsAlsoRecipient;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

/**
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
final class DiscussionService extends AbstractService
{
    /**
     * Get the user's discussions list
     *
     * @return Discussion[]
     * @throws AuthenticationRequired
     */
    public function getDiscussions(): array
    {
        $this->client->mustBeAuthenticated();

        $discussions = array_map(static function (array $discussionData) : Discussion {
            return new Discussion($discussionData);
        }, $this->client->get('discussions'));

        return $discussions;
    }

    /**
     * Get a discussion based on its id
     *
     * @throws NotFound
     * @throws AuthenticationRequired
     */
    public function getDiscussion(int $discussionId): Discussion
    {
        $this->client->mustBeAuthenticated();

        try {
            return new Discussion($this->client->get('discussions/'.$discussionId));
        } catch (ClientException $e) {
            throw new NotFound('The discussion '.$discussionId.' was not found.', $e);
        }
    }

    /**
     * Start a discussion with a vendor about a specific product.
     *
     * @throws ProductNotFound
     * @throws SenderIsAlsoRecipient
     * @throws AuthenticationRequired
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
     * @throws NotFound
     * @throws SenderIsAlsoRecipient
     * @throws AuthenticationRequired
     */
    public function startDiscussionWithVendor(int $companyId): Discussion
    {
        $this->client->mustBeAuthenticated();

        try {
            $discussionData = $this->client->post('discussions', [RequestOptions::JSON => ['companyId' => $companyId]]);

            return new Discussion($discussionData);
        } catch (ClientException $e) {
            throw new NotFound('Company '.$companyId.' has not been found.', $e);
        }
    }

    /**
     * Start a discussion with a vendor about a specific product identified by its declination ID.
     *
     * @throws ProductNotFound
     * @throws SenderIsAlsoRecipient
     * @throws AuthenticationRequired
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
     * @return Message[]
     * @throws AuthenticationRequired
     */
    public function getMessages(int $discussionId): array
    {
        $this->client->mustBeAuthenticated();

        try {
            $messages = $this->client->get('discussions/'.$discussionId.'/messages');
            $userId = $this->client->getApiKey()->getId();

            return array_map(static function (array $messageData) use ($userId) : Message {
                $messageData['isAuthor'] = ($messageData['authorId'] === $userId);

                return new Message($messageData);
            }, $messages);
        } catch (ClientException $e) {
            throw new NotFound('The discussion '.$discussionId.' was not found.', $e);
        }
    }

    /**
     * Post a new message in the discussion
     *
     * @throws SomeParametersAreInvalid
     * @throws AuthenticationRequired
     */
    public function postMessage(int $discussionId, string $content): Message
    {
        $this->client->mustBeAuthenticated();

        try {
            $messageData = $this->client->post('discussions/'.$discussionId.'/messages', [RequestOptions::JSON => ['content' => $content]]);
            $messageData['isAuthor'] = ($messageData['authorId'] === $this->client->getApiKey()->getId());

            return new Message($messageData);
        } catch (ClientException $e) {
            throw new SomeParametersAreInvalid("Some parameters are invalid", 400, $e);
        }
    }

    public function submitContactRequest(string $senderEmail, string $subject, string $message): void
    {
        $this->client->post('contact-request', [
            RequestOptions::JSON => [
                'senderEmail' => $senderEmail,
                'subject' => $subject,
                'message' => $message,
            ],
        ]);
    }
}
