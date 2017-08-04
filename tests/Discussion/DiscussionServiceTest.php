<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);


namespace Wizaplace\Tests\Discussion;

use Wizaplace\Discussion\Discussion;
use Wizaplace\Discussion\DiscussionService;
use Wizaplace\Discussion\Message;
use Wizaplace\Exception\NotFound;
use Wizaplace\Tests\ApiTestCase;

class DiscussionServiceTest extends ApiTestCase
{
    /**
     * @var $discussionService DiscussionService
     */
    private $discussionService;

    public function setUp(): void
    {
        parent::setUp();
        $this->discussionService = $this->buildDiscussionService();
    }

    public function testStartDiscussion()
    {
        $discussion = $this->discussionService->startDiscussion(1);

        $expectedDiscussion = new Discussion(
            [
                'id' => 1,
                'recipient' => 'Test company',
                'productId' => 1,
                'title' => 'A propos du produit optio corporis similique voluptatum',
                'unreadCount' => 0,
            ]
        );

        $this->assertEquals($expectedDiscussion, $discussion);
    }

    public function testListDiscussions()
    {
        $discussions = $this->discussionService->listDiscussions();

        $expectedDiscussions = [
            new Discussion(
                [
                    'id' => 1,
                    'recipient' => 'Test company',
                    'productId' => 1,
                    'title' => 'A propos du produit optio corporis similique voluptatum',
                    'unreadCount' => 0,
                ]
            ),
        ];

        $this->assertEquals($expectedDiscussions, $discussions);
    }

    public function testGetDiscussion()
    {
        $discussion = $this->discussionService->getDiscussion(1);

        $expectedDiscussion = new Discussion(
            [
                'id' => 1,
                'recipient' => 'Test company',
                'productId' => 1,
                'title' => 'A propos du produit optio corporis similique voluptatum',
                'unreadCount' => 0,
            ]
        );

        $this->assertEquals($expectedDiscussion, $discussion);
    }

    public function testGetInexistantDiscussion()
    {
        $this->expectException(NotFound::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('The discussion 2 was not found.');

        $this->discussionService->getDiscussion(2);
    }

    public function testGetEmptyMessages()
    {
        $messages = $this->discussionService->listMessages(1);

        $this->assertEquals([], $messages);
    }

    public function testGetMessagesFromInexistantDiscussion()
    {
        $this->expectException(NotFound::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('The discussion 2 was not found.');

        $this->discussionService->listMessages(2);
    }

    public function testPostMessage()
    {
        $message = $this->discussionService->postMessage(1, 'This is a test message');

        $date = new \DateTimeImmutable();

        $expectedMessage = new Message(
            [
                'author' => 3,
                'content' => 'This is a test message',
                'date' => $date->format(DATE_RFC3339),
            ]
        );

        $this->assertEquals($expectedMessage->getAuthorId(), $message->getAuthorId());
        $this->assertEquals($expectedMessage->getContent(), $message->getContent());
        $this->assertInstanceOf(\DateTimeImmutable::class, $message->getDate());
    }

    public function testGetMessages()
    {
        $this->discussionService->postMessage(1, 'This is an other test message');

        $messages = $this->discussionService->listMessages(1);

        $date = new \DateTimeImmutable();

        $expectedMessages = [
            new Message([
                'author' => 3,
                'content' => 'This is a test message',
                'date' => $date->format(DATE_RFC3339),
            ]),
            new Message([
                'author' => 3,
                'content' => 'This is an other test message',
                'date' => $date->format(DATE_RFC3339),
            ]),
        ];

        $this->assertEquals($expectedMessages[0]->getAuthorId(), $messages[0]->getAuthorId());
        $this->assertEquals($expectedMessages[0]->getContent(), $messages[0]->getContent());
        $this->assertInstanceOf(\DateTimeImmutable::class, $messages[0]->getDate());
        $this->assertEquals($expectedMessages[1]->getAuthorId(), $messages[1]->getAuthorId());
        $this->assertEquals($expectedMessages[1]->getContent(), $messages[1]->getContent());
        $this->assertInstanceOf(\DateTimeImmutable::class, $messages[1]->getDate());
    }

    private function buildDiscussionService(): DiscussionService
    {
        $client = $this->buildApiClient();
        $client->authenticate('user@wizaplace.com', 'password');

        return new DiscussionService($client);
    }
}
