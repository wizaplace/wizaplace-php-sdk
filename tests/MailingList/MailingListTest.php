<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\MailingList;

use Wizaplace\MailingList\Exception\MailingListDoesNotExist;
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
        $this->mlService = $this->buildMailingListService();
    }

    public function testGetMailingList()
    {
        $mailingLists = $this->mlService->getMailingLists();
        foreach ($mailingLists as $mailingList) {
            $this->assertInstanceOf(MailingList::class, $mailingList);
        }

        $this->assertSame(1, count($mailingLists));
        $this->assertSame(1, $mailingLists[0]->getId());
        $this->assertSame('Newsletter', $mailingLists[0]->getName());
    }

    public function testSubscribe()
    {
        $this->mlService->subscribe(1, 'user@wizaplace.com');

        $this->assertCount(1, static::$historyContainer);

        $this->mlService->unsubscribe(1, 'user@wizaplace.com');
    }

    public function testSubscribeAlreadySubscribed()
    {
        $this->expectException(UserAlreadySubscribed::class);

        $this->mlService->subscribe(1, 'admin@wizaplace.com');
        $this->mlService->subscribe(1, 'admin@wizaplace.com');

        $this->mlService->unsubscribe(1, 'admin@wizaplace.com');
    }

    public function testUnsubscribe()
    {
        $this->mlService->subscribe(1, 'user@wizaplace.com');

        $this->mlService->unsubscribe(1, 'user@wizaplace.com');

        $this->assertCount(2, static::$historyContainer);
    }

    public function testSubscribeToNotAList()
    {
        $this->expectException(MailingListDoesNotExist::class);

        $this->mlService->subscribe(2, 'user@wizaplace.com');
    }

    private function buildMailingListService(): MailingListService
    {
        return new MailingListService($this->buildApiClient());
    }
}
