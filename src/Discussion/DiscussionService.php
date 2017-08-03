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

class DiscussionService extends AbstractService
{
    /**
     * @return Discussion[]
     */
    public function getDiscussions(): array
    {
        $discussions = array_map(function ($discussionData){
            return new Discussion($discussionData);
        }, $this->client->get('discussions'));

        return $discussions;
    }

    public function getDiscussion(int $discussionId): Discussion
    {
        try {
            return new Discussion($this->client->get('discussions/'.$discussionId));
        } catch (ClientException $e) {
            throw new NotFound('The discussion '.$discussionId.' was not found.');
        }
    }

    public function startDiscussion(int $productId): Discussion
    {
        try {
            $discussionData = $this->client->post('discussions', ['json' => ['productId' => $productId]]);

            return new Discussion($discussionData);
        } catch (ClientException $e) {
            throw new SomeParametersAreInvalid();
        }
    }

    /**
     * @return Message[]
     */
    public function getMessages(int $discussionId): array
    {
        try {
            $messages = $this->client->get('discussions/'.$discussionId.'/messages');

            return array_map(function($messageData){
                return new Message($messageData);
            }, $messages);
        } catch (ClientException $e) {
            throw new NotFound('The discussion '.$discussionId.' was not found.');
        }
    }

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
