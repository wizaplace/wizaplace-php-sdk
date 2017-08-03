<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);


namespace Wizaplace\tests\Discussion;

use Wizaplace\Discussion\DiscussionService;
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

        $this->assertEquals(true, $discussion);
    }

    public function testListDiscussions()
    {
        $discussions = $this->discussionService->getDiscussions();

        $this->assertEquals([], $discussions);
    }

    public function testGetDiscussion()
    {
        $discussion = $this->discussionService->getDiscussion(1);

        $this->assertNotNull($discussion);
    }

    private function buildDiscussionService(): DiscussionService
    {
        $client = $this->buildApiClient();
        $client->authenticate('user@wizaplace.com', 'password');

        return new DiscussionService($client);
    }
}
