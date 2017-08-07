<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Discussion;

use GuzzleHttp\Exception\ClientException;
use Wizaplace\AbstractService;
use Wizaplace\Exception\NotFound;
use Wizaplace\Exception\SomeParametersAreInvalid;

/**
 * This service helps getting and creating discussions and messages.
 *
 * Discussion are a list of exchanged messages between a customer and a product's vendor.
 *
 * Example :
 *
 *      // Get the user's discussions list
 *      $discussionsList = $discussionService->listDiscussions();
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
     * @return Discussion[]
     */
    public function listDiscussions(): array
    {
        $discussions = array_map(function (array $discussionData): Discussion {
            return new Discussion($discussionData);
        }, $this->client->get('discussions'));

        return $discussions;
    }

    /** Get a discussion based on its id */
    public function getDiscussion(int $discussionId): Discussion
    {
        try {
            return new Discussion($this->client->get('discussions/'.$discussionId));
        } catch (ClientException $e) {
            throw new NotFound('The discussion '.$discussionId.' was not found.');
        }
    }

    /** Start a discussion with a vendor about a specific product. */
    public function startDiscussion(int $productId): Discussion
    {
        try {
            $discussionData = $this->client->post('discussions', ['json' => ['productId' => $productId]]);

            return new Discussion($discussionData);
        } catch (ClientException $e) {
            throw new NotFound('The product '.$productId.' has not been found.');
        }
    }

    /**
     * Get the discussion's messages list
     * @return Message[]
     */
    public function listMessages(int $discussionId): array
    {
        try {
            $messages = $this->client->get('discussions/'.$discussionId.'/messages');

            return array_map(function (array $messageData): Message {
                return new Message($messageData);
            }, $messages);
        } catch (ClientException $e) {
            throw new NotFound('The discussion '.$discussionId.' was not found.');
        }
    }

    /** Post a new message in the discussion */
    public function postMessage(int $discussionId, string $content): Message
    {
        try {
            $messageData = $this->client->post('discussions/'.$discussionId.'/messages', ['json' => ['content' => $content]]);

            return new Message($messageData);
        } catch (ClientException $e) {
            throw new SomeParametersAreInvalid();
        }
    }
}
