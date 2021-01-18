<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Discussion;

use GuzzleHttp\Psr7\Response;
use Wizaplace\SDK\Catalog\DeclinationId;
use Wizaplace\SDK\Discussion\Discussion;
use Wizaplace\SDK\Discussion\DiscussionService;
use Wizaplace\SDK\Discussion\Message;
use Wizaplace\SDK\Exception\CompanyHasNoAdministrator;
use Wizaplace\SDK\Exception\CompanyNotFound;
use Wizaplace\SDK\Exception\DiscussionNotFound;
use Wizaplace\SDK\Exception\ProductNotFound;
use Wizaplace\SDK\Exception\SenderIsAlsoRecipient;
use Wizaplace\SDK\Tests\ApiTestCase;

final class DiscussionServiceTest extends ApiTestCase
{
    /**
     * @var DiscussionService
     */
    private $discussionService;

    public function setUp(): void
    {
        parent::setUp();
        $this->discussionService = $this->buildDiscussionService('customer-1@world-company.com', 'password-customer-1');
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
        $this->expectException(ProductNotFound::class);
        $this->expectExceptionCode(404);

        $this->discussionService->startDiscussion(42);
    }

    public function testStartDiscussionOnOwnProduct()
    {
        $this->expectException(SenderIsAlsoRecipient::class);

        $this->buildDiscussionService('vendor@world-company.com', 'password-vendor')->startDiscussion(3);
    }

    public function testStartDiscussionWithVendor()
    {
        $discussion = $this->discussionService->startDiscussionWithVendor(2);

        $expectedDiscussion = new Discussion(
            [
                'id' => 2,
                'recipient' => 'ACME',
                'productId' => 0,
                'title' => 'Contact ACME',
                'unreadCount' => 0,
            ]
        );

        $this->assertEquals($expectedDiscussion, $discussion);

        // Check that the vendor can access the discussion
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('vendor@wizaplace.com', 'password');
        $discussions = (new DiscussionService($apiClient))->getDiscussions();
        $this->assertContainsOnly(Discussion::class, $discussions);
        $this->assertCount(1, $discussions);
        $discussion = reset($discussions);
        $this->assertSame($expectedDiscussion->getId(), $discussion->getId());
    }

    public function testStartDiscussionWithOwnCompany()
    {
        $this->expectException(SenderIsAlsoRecipient::class);

        $this->buildDiscussionService('vendor@world-company.com', 'password-vendor')->startDiscussionWithVendor(3);
    }

    public function testStartDiscussionWithCompanyWithoutAdmin()
    {
        $this->expectException(CompanyHasNoAdministrator::class);

        $this->buildDiscussionService()->startDiscussionWithVendor(1);
    }

    public function testStartDiscussionOnInexistentVendor()
    {
        $this->expectExceptionCode(404);
        $this->expectException(CompanyNotFound::class);

        $this->discussionService->startDiscussionWithVendor(404);
    }

    public function testStartDiscussionFromDeclinationId()
    {
        $discussion = $this->discussionService->startDiscussionFromDeclinationId(new DeclinationId('1_0'));

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

    public function testStartDiscussionOnOwnDeclination()
    {
        $this->expectException(SenderIsAlsoRecipient::class);

        $this->buildDiscussionService('vendor@world-company.com', 'password-vendor')->startDiscussionFromDeclinationId(new DeclinationId('3_9_10'));
    }

    public function testStartDiscussionOnInexistantDeclinationId()
    {
        $this->expectExceptionCode(404);
        $this->expectException(ProductNotFound::class);

        $this->discussionService->startDiscussionFromDeclinationId(new DeclinationId('404_1_2'));
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
                    'title' => 'A propos du produit Corsair Gaming VOID Pro RGB Dolby 7.1 Sans fil - Edition Carbon',
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
                'title' => 'A propos du produit Corsair Gaming VOID Pro RGB Dolby 7.1 Sans fil - Edition Carbon',
                'unreadCount' => 0,
            ]
        );

        $this->assertEquals($expectedDiscussion, $discussion);
    }

    public function testGetInexistantDiscussion()
    {
        $this->expectException(DiscussionNotFound::class);

        $this->discussionService->getDiscussion(2);
    }

    public function testGetEmptyMessages()
    {
        $messages = $this->discussionService->getMessages(1);

        $this->assertSame([], $messages);
    }

    public function testGetMessagesFromInexistantDiscussion()
    {
        $this->expectException(DiscussionNotFound::class);

        $this->discussionService->getMessages(2);
    }

    public function testPostMessage()
    {
        $discussion = $this->discussionService->startDiscussionWithVendor(2);

        $message = $this->discussionService->postMessage($discussion->getId(), 'This is a test message');

        $date = new \DateTimeImmutable();

        $expectedMessage = new Message(
            [
                'isAuthor' => true,
                'content' => 'This is a test message',
                'date' => $date->format(DATE_RFC3339),
                'authorId' => 7,
                'attachments' => []
            ]
        );

        $this->assertSame($expectedMessage->getContent(), $message->getContent());
        $this->assertInstanceOf(\DateTimeImmutable::class, $message->getDate());
    }

    public function testPostMessageWithAttachment()
    {
        $discussion = $this->discussionService->startDiscussionWithVendor(2);

        $attachments = [];

        $attachments[] = $this->mockUploadedFile("minimal.pdf");

        $message = $this->discussionService->postMessage($discussion->getId(), 'This is a test message', $attachments);

        static::assertCount(1, $message->getAttachments());

        $date = new \DateTimeImmutable();

        $expectedMessage = new Message(
            [
                'isAuthor' => true,
                'content' => 'This is a test message',
                'date' => $date->format(DATE_RFC3339),
                'authorId' => $message->getAuthorId(),
                'attachments' => $message->getAttachments()
            ]
        );

        static::assertSame($expectedMessage->getContent(), $message->getContent());
        static::assertInstanceOf(\DateTimeImmutable::class, $message->getDate());
    }

    public function testPostMessageWithMoreThanOneAttachment()
    {
        $discussion = $this->discussionService->startDiscussionWithVendor(2);

        $attachments = [];

        $attachments[] = $this->mockUploadedFile("minimal.pdf");
        $attachments[] = $this->mockUploadedFile("favicon.png");

        $message = $this->discussionService->postMessage($discussion->getId(), 'This is a test message', $attachments);

        static::assertCount(2, $message->getAttachments());

        $date = new \DateTimeImmutable();

        $expectedMessage = new Message(
            [
                'isAuthor' => true,
                'content' => 'This is a test message',
                'date' => $date->format(DATE_RFC3339),
                'authorId' => $message->getAuthorId(),
                'attachments' => $message->getAttachments()
            ]
        );

        static::assertSame($expectedMessage->getContent(), $message->getContent());
        static::assertInstanceOf(\DateTimeImmutable::class, $message->getDate());
    }

    public function testPostMessageWithWrongAttachmentExtension()
    {
        $discussion = $this->discussionService->startDiscussionWithVendor(2);

        $attachments = [];

        $attachments[] = $this->mockUploadedFile("video.avi");

        static::expectExceptionMessage('Some parameters are invalid');
        static::expectExceptionCode(400);
        $this->discussionService->postMessage($discussion->getId(), 'This is a test message', $attachments);
    }

    public function testPostMessageWithoutAttachments()
    {
        $discussion = $this->discussionService->startDiscussionWithVendor(2);

        $message = $this->discussionService->postMessage($discussion->getId(), 'This is a test message');

        static::assertCount(0, $message->getAttachments());

        $date = new \DateTimeImmutable();

        $expectedMessage = new Message(
            [
                'isAuthor' => true,
                'content' => 'This is a test message',
                'date' => $date->format(DATE_RFC3339),
                'authorId' => $message->getAuthorId(),
                'attachments' => []
            ]
        );

        static::assertSame($expectedMessage->getContent(), $message->getContent());
        static::assertInstanceOf(\DateTimeImmutable::class, $message->getDate());
    }

    public function testPostMessageOnInexistantDiscussion()
    {
        $this->expectException(DiscussionNotFound::class);

        $this->discussionService->postMessage(8, 'This is a test message');
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
        $this->assertSame(7, $messages[1]->getAuthorId());

        $this->assertSame('This is an other test message', $messages[1]->getContent());
        $this->assertInstanceOf(\DateTimeImmutable::class, $messages[1]->getDate());
        $this->assertGreaterThan(1500000000, $messages[1]->getDate()->getTimestamp());
        $this->assertTrue($messages[1]->isAuthor());
        $this->assertSame(7, $messages[1]->getAuthorId());
    }

    public function testSubmitContactRequest()
    {
        $message = <<<MSG
Last Name: Bond
First Name: James
Email: client@example.com
Phone: 0123456798

Message:
    I love your marketplace!
    Keep up the good work!
MSG;

        $service = $this->buildDiscussionService('customer-1@world-company.com', 'password-customer-1');

        static::$historyContainer = [];

        $service->submitContactRequest(
            'client@example.com',
            'Contact form: Great marketplace!',
            $message
        );

        // We don't have a way to check that the contact request was processed.
        // So we just check that an HTTP request was made successfully
        $this->assertCount(1, static::$historyContainer);
        /** @var Response $response */
        $response = static::$historyContainer[0]['response'];
        $this->assertSame(201, $response->getStatusCode());
    }

    public function testSubmitContactRequestWithAttachments(): void
    {
        $message = <<<MSG
Last Name: Bond
First Name: James
Email: client@example.com
Phone: 0123456798

Message:
    I love your marketplace!
    Keep up the good work!
MSG;

        $service = $this->buildDiscussionService('customer-1@world-company.com', 'password-customer-1');

        $service->submitContactRequest(
            'client@example.com',
            'Contact form: Great marketplace!',
            $message,
            "recipientEmail@recipientEmail.fr",
            ["https://www.wizaplace.com/wp-content/uploads/2018/03/Logo-400x94.png"],
            [
                $this->mockUploadedFile('dummy.txt'),
                $this->mockUploadedFile('minimal.pdf'),
            ]
        );

        // We don't have a way to check that the contact request was processed.
        // So we just check that an HTTP request was made successfully
        $this->assertCount(3, static::$historyContainer);
        /** @var Response $response */
        $response = static::$historyContainer[2]['response'];
        $this->assertSame(201, $response->getStatusCode());
    }

    private function buildDiscussionService($email = 'customer-1@world-company.com', $password = 'password-customer-1'): DiscussionService
    {
        $client = $this->buildApiClient();
        $client->authenticate($email, $password);

        return new DiscussionService($client);
    }
}
