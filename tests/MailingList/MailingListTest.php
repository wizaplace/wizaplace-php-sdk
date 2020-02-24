<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\MailingList;

use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\MailingList\Exception\MailingListDoesNotExist;
use Wizaplace\SDK\MailingList\Exception\UserAlreadySubscribed;
use Wizaplace\SDK\MailingList\MailingList;
use Wizaplace\SDK\MailingList\MailingListService;
use Wizaplace\SDK\Tests\ApiTestCase;

final class MailingListTest extends ApiTestCase
{
    /**
     * @var MailingListService
     */
    private $mlService;

    private const USER_EMAIL = 'user@wizaplace.com';

    public function setUp(): void
    {
        parent::setUp();
        $this->mlService = $this->buildMailingListService();
    }

    public function testGetMailingList()
    {
        $mailingLists = $this->mlService->getMailingLists();
        foreach ($mailingLists as $mailingList) {
            $this->assertInstanceOf(MailingList::class, $mailingList);
        }

        $this->assertSame(1, \count($mailingLists));
        $this->assertSame(1, $mailingLists[0]->getId());
        $this->assertSame('Newsletter', $mailingLists[0]->getName());
    }

    public function testSubscribeThenUnsubscribe()
    {
        $this->assertFalse($this->mlService->isSubscribed(1));

        $this->mlService->subscribe(1, self::USER_EMAIL);

        $this->assertTrue($this->mlService->isSubscribed(1));

        $this->mlService->unsubscribe(1, self::USER_EMAIL);

        $this->assertFalse($this->mlService->isSubscribed(1));
    }

    public function testIsSubscribedWithoutAuthentication()
    {
        $this->expectException(AuthenticationRequired::class);
        (new MailingListService($this->buildApiClient()))->isSubscribed(1);
    }

    public function testSubscribeAlreadySubscribed()
    {

        $this->mlService->subscribe(1, 'admin@wizaplace.com');

        try {
            $this->expectException(UserAlreadySubscribed::class);
            $this->mlService->subscribe(1, 'admin@wizaplace.com');
        } finally {
            $this->mlService->unsubscribe(1, 'admin@wizaplace.com');
        }
    }

    public function testSubscribeToNotAList()
    {
        $this->expectException(MailingListDoesNotExist::class);

        $this->mlService->subscribe(2, 'user@wizaplace.com');
    }

    private function buildMailingListService(): MailingListService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate(self::USER_EMAIL, 'password');

        return new MailingListService($apiClient);
    }
}
