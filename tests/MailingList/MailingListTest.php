<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\MailingList;

use Wizaplace\MailingList\Exception\UserAlreadySubscribed;
use Wizaplace\MailingList\MailingList;
use Wizaplace\MailingList\MailingListService;
use Wizaplace\Tests\ApiTestCase;

class MailingListTest extends ApiTestCase
{
    /**
     * @var MailingListService
     */
    private $mlService;

    public function setUp(): void
    {
        parent::setUp();
        $this->mlService = new MailingListService($this->getGuzzleClient());
        $this->mlService->subscribeToMailingList(2, 'user@wizaplace.com');
    }

    public function testGetMailingList()
    {
        $mailingLists = $this->mlService->getMailingList();

        foreach ($mailingLists as $mailingList) {
            $this->assertInstanceOf(MailingList::class, $mailingList);
        }
    }

    public function testSubscribeUserToMailingList()
    {
        $this->mlService->subscribeToMailingList(1, 'user@wizaplace.com');

        $this->assertCount(2, static::$historyContainer);
    }

    public function testSubscribeAlreadySubscribedUserToMailingList()
    {
        $this->expectException(UserAlreadySubscribed::class);

        $this->mlService->subscribeToMailingList(2, 'user@wizaplace.com');
    }

    public function testUnsubscribeUser()
    {
        $this->mlService->unsubscribeFromMailingList(1, 'user@wizaplace.com');

        $this->assertCount(2, static::$historyContainer);
    }

    public function tearDown(): void
    {
        $this->mlService->unsubscribeFromMailingList(1, 'user@wizaplace.com');
        $this->mlService->unsubscribeFromMailingList(2, 'user@wizaplace.com');
        parent::tearDown();
    }
}
