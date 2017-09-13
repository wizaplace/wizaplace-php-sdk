<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);


namespace Wizaplace\SDK\Tests\Discussion;

use Wizaplace\SDK\Discussion\Discussion;
use Wizaplace\SDK\Discussion\DiscussionService;
use Wizaplace\SDK\Discussion\Message;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Tests\ApiTestCase;

final class DiscussionServiceTest extends ApiTestCase
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
                'id' => 2,
                'recipient' => 'The World Company Inc.',
                'productId' => 1,
                'title' => 'A propos du produit Z11 Plus Boîtier PC en Acier ATX',
                'unreadCount' => 0,
            ]
        );

        $this->assertEquals($expectedDiscussion, $discussion);
    }

    public function testStartDiscussionOnInexistantProduct()
    {
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('The product 42 has not been found.');

        $this->discussionService->startDiscussion(42);
    }

    public function testListDiscussions()
    {
        $discussions = $this->discussionService->getDiscussions();

        $expectedDiscussions = [
            new Discussion(
                [
                    'id' => 1,
                    'recipient' => 'The World Company Inc.',
                    'productId' => 4,
                    'title' => 'A propos du produit Product with shippings',
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
                'recipient' => 'The World Company Inc.',
                'productId' => 4,
                'title' => 'A propos du produit Product with shippings',
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
        $messages = $this->discussionService->getMessages(1);

        $this->assertSame([], $messages);
    }

    public function testGetMessagesFromInexistantDiscussion()
    {
        $this->expectException(NotFound::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('The discussion 2 was not found.');

        $this->discussionService->getMessages(2);
    }

    public function testPostMessage()
    {
        $message = $this->discussionService->postMessage(1, 'This is a test message');

        $date = new \DateTimeImmutable();

        $expectedMessage = new Message(
            [
                'isAuthor' => true,
                'content' => 'This is a test message',
                'date' => $date->format(DATE_RFC3339),
            ]
        );

        $this->assertSame($expectedMessage->getContent(), $message->getContent());
        $this->assertInstanceOf(\DateTimeImmutable::class, $message->getDate());
    }

    public function testGetMessages()
    {
        $this->discussionService->postMessage(1, 'This is a test message');
        $this->discussionService->postMessage(1, 'This is an other test message');

        $messages = $this->discussionService->getMessages(1);

        $this->assertSame('This is a test message', $messages[0]->getContent());
        $this->assertInstanceOf(\DateTimeImmutable::class, $messages[0]->getDate());
        $this->assertGreaterThan(1500000000, $messages[0]->getDate()->getTimestamp());
        $this->assertTrue($messages[0]->isAuthor());

        $this->assertSame('This is an other test message', $messages[1]->getContent());
        $this->assertInstanceOf(\DateTimeImmutable::class, $messages[1]->getDate());
        $this->assertGreaterThan(1500000000, $messages[1]->getDate()->getTimestamp());
        $this->assertTrue($messages[1]->isAuthor());
    }

    private function buildDiscussionService(): DiscussionService
    {
        $client = $this->buildApiClient();
        $client->authenticate('customer-1@world-company.com', 'password-customer-1');

        return new DiscussionService($client);
    }
}
